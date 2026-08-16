<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CatalogBundle;
use App\Models\CatalogProduct;
use App\Models\Customer;
use App\Models\EmailCampaign;
use App\Models\EmailCampaignRecipient;
use App\Services\CampaignMailer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Throwable;

class EmailCampaignController extends Controller
{
    public function index(): View
    {
        return view('admin.email-campaigns.index', [
            'campaigns' => EmailCampaign::query()->latest()->paginate(15),
        ]);
    }

    public function create(): View
    {
        return $this->formView(new EmailCampaign(['status' => 'draft']));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $campaign = DB::transaction(function () use ($request, $data) {
            $campaign = EmailCampaign::create($this->payload($request, $data));
            $this->syncRecipients($campaign, $request);
            return $campaign;
        });

        return redirect()->route('admin.mailing.edit', $campaign)->with('status', 'Campaña creada. Revisa la vista previa antes de enviarla.');
    }

    public function edit(EmailCampaign $mailing): View
    {
        return $this->formView($mailing->load('recipients'));
    }

    public function update(Request $request, EmailCampaign $mailing): RedirectResponse
    {
        abort_if($mailing->status === 'sending', 409, 'La campaña se está enviando.');
        $data = $this->validated($request);
        DB::transaction(function () use ($request, $mailing, $data) {
            $mailing->update($this->payload($request, $data, $mailing));
            $this->syncRecipients($mailing, $request);
        });

        return back()->with('status', 'Campaña actualizada correctamente.');
    }

    public function preview(EmailCampaign $mailing): View
    {
        return view('emails.campaign', $this->emailData($mailing));
    }

    public function send(EmailCampaign $mailing, CampaignMailer $mailer): RedirectResponse
    {
        abort_if($mailing->status === 'sending', 409, 'La campaña ya se está enviando.');
        $pending = $mailing->recipients()->whereIn('status', ['pending', 'failed'])->limit(200)->get();
        if ($pending->isEmpty()) {
            return back()->withErrors(['recipients' => 'No hay destinatarios pendientes para enviar.']);
        }

        $mailing->update(['status' => 'sending']);
        foreach ($pending as $recipient) {
            try {
                if (! $recipient->tracking_token) {
                    $recipient->update(['tracking_token' => Str::random(48)]);
                }
                $html = view('emails.campaign', $this->emailData($mailing, $recipient))->render();
                $html = $this->trackLinks($html, $recipient);
                $mailer->send($recipient, $mailing->subject, $html);
                $recipient->update(['status' => 'sent', 'sent_at' => now(), 'error_message' => null]);
            } catch (Throwable $exception) {
                report($exception);
                $recipient->update(['status' => 'failed', 'error_message' => Str::limit($exception->getMessage(), 900)]);
            }
        }

        $sent = $mailing->recipients()->where('status', 'sent')->count();
        $failed = $mailing->recipients()->where('status', 'failed')->count();
        $mailing->update([
            'status' => $failed ? 'partial' : 'sent',
            'sent_count' => $sent,
            'failed_count' => $failed,
            'sent_at' => now(),
        ]);

        return back()->with('status', "Envío terminado: {$sent} entregados al servidor y {$failed} con error.");
    }

    public function destroy(EmailCampaign $mailing): RedirectResponse
    {
        $mailing->delete();
        return redirect()->route('admin.mailing.index')->with('status', 'Campaña eliminada.');
    }

    private function formView(EmailCampaign $campaign): View
    {
        return view('admin.email-campaigns.form', [
            'campaign' => $campaign,
            'products' => CatalogProduct::active()->with('photos')->orderBy('name')->get(),
            'bundles' => CatalogBundle::active()->with('photos')->orderBy('name')->get(),
            'customers' => Customer::query()->whereNotNull('email')->where('email', '!=', '')->orderBy('name')->get(),
        ]);
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:160'],
            'subject' => ['required', 'string', 'max:190'],
            'preview_text' => ['nullable', 'string', 'max:190'],
            'content_html' => ['nullable', 'string', 'max:150000'],
            'featured_item' => ['nullable', 'string', 'max:80'],
            'related_product_ids' => ['nullable', 'array', 'max:6'],
            'related_product_ids.*' => ['integer', Rule::exists('catalog_products', 'id')],
            'customer_ids' => ['nullable', 'array'],
            'customer_ids.*' => ['integer', Rule::exists('customers', 'id')],
            'recipient_text' => ['nullable', 'string', 'max:30000'],
            'recipient_file' => ['nullable', 'file', 'mimes:csv,txt', 'max:2048'],
        ]);
    }

    private function payload(Request $request, array $data, ?EmailCampaign $campaign = null): array
    {
        [$type, $id] = array_pad(explode(':', (string) ($data['featured_item'] ?? ''), 2), 2, null);
        if (! in_array($type, ['product', 'bundle'], true) || ! ctype_digit((string) $id)) {
            $type = $id = null;
        }

        return [
            'created_by' => $campaign?->created_by ?: $request->user()->id,
            'name' => $data['name'],
            'subject' => $data['subject'],
            'preview_text' => $data['preview_text'] ?? null,
            'content_html' => $this->normalizeContentUrls($data['content_html'] ?? null),
            'featured_type' => $type,
            'featured_id' => $id,
            'related_product_ids' => array_values($data['related_product_ids'] ?? []),
            'status' => $campaign?->status ?: 'draft',
        ];
    }

    private function syncRecipients(EmailCampaign $campaign, Request $request): void
    {
        $recipients = collect();
        Customer::query()->whereIn('id', $request->input('customer_ids', []))->get()->each(
            fn (Customer $customer) => $recipients->push(['name' => $customer->name, 'email' => $customer->email])
        );

        $raw = (string) $request->input('recipient_text', '');
        if ($request->hasFile('recipient_file')) {
            $raw .= "\n".file_get_contents($request->file('recipient_file')->getRealPath());
        }

        collect(preg_split('/\R/', $raw))->each(function ($line) use ($recipients) {
            $columns = str_getcsv(trim((string) $line));
            if (count($columns) === 1) {
                $recipients->push(['name' => null, 'email' => trim($columns[0] ?? '')]);
            } else {
                $recipients->push(['name' => trim($columns[0]), 'email' => trim($columns[1])]);
            }
        });

        $recipients = $recipients->filter(fn ($item) => filter_var($item['email'] ?? null, FILTER_VALIDATE_EMAIL))
            ->unique(fn ($item) => Str::lower($item['email']))->take(200)->values();

        if ($recipients->isNotEmpty()) {
            $campaign->recipients()->delete();
            $campaign->recipients()->createMany($recipients->map(fn ($item) => $item + ['status' => 'pending', 'tracking_token' => Str::random(48)])->all());
        }
        $campaign->update([
            'recipient_count' => $campaign->recipients()->count(),
            'sent_count' => $campaign->recipients()->where('status', 'sent')->count(),
            'failed_count' => $campaign->recipients()->where('status', 'failed')->count(),
        ]);
    }

    private function emailData(EmailCampaign $campaign, ?EmailCampaignRecipient $recipient = null): array
    {
        $featured = match ($campaign->featured_type) {
            'product' => CatalogProduct::with('photos')->find($campaign->featured_id),
            'bundle' => CatalogBundle::with('photos')->find($campaign->featured_id),
            default => null,
        };
        $related = CatalogProduct::query()->whereIn('id', $campaign->related_product_ids ?? [])->get();

        $recipientName = $recipient?->name;

        return compact('campaign', 'featured', 'related', 'recipientName', 'recipient');
    }

    private function trackLinks(string $html, EmailCampaignRecipient $recipient): string
    {
        $document = new \DOMDocument('1.0', 'UTF-8');
        $previous = libxml_use_internal_errors(true);
        $document->loadHTML('<?xml encoding="UTF-8">'.$html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        foreach ($document->getElementsByTagName('a') as $link) {
            $url = $link->getAttribute('href');
            if (filter_var($url, FILTER_VALIDATE_URL) && in_array(parse_url($url, PHP_URL_SCHEME), ['http', 'https'], true)) {
                $link->setAttribute('href', URL::signedRoute('mailing.track.click', ['token' => $recipient->tracking_token, 'url' => $url]));
            }
        }
        $tracked = $document->saveHTML();
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        return preg_replace('/^<\?xml encoding="UTF-8"\?>/', '', $tracked) ?: $html;
    }

    private function normalizeContentUrls(?string $html): ?string
    {
        if (! $html) {
            return $html;
        }

        $imagesRoot = rtrim(url('/images'), '/').'/';
        $html = preg_replace('~(["\'])(?:https?://[^/"\']+)?/admin/images/~i', '$1'.$imagesRoot, $html);
        $html = preg_replace('~(src=["\'])(?:\.\./)*images/~i', '$1'.$imagesRoot, $html);

        return $html;
    }
}
