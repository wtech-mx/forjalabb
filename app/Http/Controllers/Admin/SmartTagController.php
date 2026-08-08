<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SmartTag;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SmartTagController extends Controller
{
    public function index(Request $request): View
    {
        $tags = SmartTag::query()
            ->when($request->filled('type'), fn ($query) => $query->where('type', $request->string('type')))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('admin.tags.index', compact('tags'));
    }

    public function create(Request $request): View
    {
        $type = $request->query('type', SmartTag::TYPE_BIKER);

        abort_unless(in_array($type, [SmartTag::TYPE_BIKER, SmartTag::TYPE_DOG], true), 404);

        return view('admin.tags.form', [
            'tag' => new SmartTag(['type' => $type, 'is_active' => true]),
            'type' => $type,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $tag = SmartTag::create($this->validated($request));

        return redirect()
            ->route('admin.tags.show', $tag)
            ->with('status', 'Tag creado. El QR ya apunta al perfil publico.');
    }

    public function show(SmartTag $tag): View
    {
        return view('admin.tags.show', compact('tag'));
    }

    public function edit(SmartTag $tag): View
    {
        return view('admin.tags.form', [
            'tag' => $tag,
            'type' => $tag->type,
        ]);
    }

    public function update(Request $request, SmartTag $tag): RedirectResponse
    {
        $tag->update($this->validated($request));

        return redirect()
            ->route('admin.tags.show', $tag)
            ->with('status', 'Tag actualizado correctamente.');
    }

    public function qr(SmartTag $tag): Response
    {
        $renderer = new ImageRenderer(
            new RendererStyle(420),
            new SvgImageBackEnd
        );

        $svg = (new Writer($renderer))->writeString($tag->public_url);

        return response($svg, 200, [
            'Content-Type' => 'image/svg+xml',
            'Content-Disposition' => 'inline; filename="'.$tag->token.'-qr.svg"',
        ]);
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'type' => ['required', Rule::in([SmartTag::TYPE_BIKER, SmartTag::TYPE_DOG])],
            'is_active' => ['nullable', 'boolean'],
            'display_name' => ['required', 'string', 'max:120'],
            'owner_name' => ['nullable', 'string', 'max:120'],
            'owner_phone' => ['nullable', 'string', 'max:30'],
            'owner_email' => ['nullable', 'email', 'max:160'],
            'secondary_contact_name' => ['nullable', 'string', 'max:120'],
            'secondary_contact_phone' => ['nullable', 'string', 'max:30'],
            'secondary_contact_email' => ['nullable', 'email', 'max:160'],
            'blood_type' => ['nullable', Rule::in(SmartTag::bloodTypes())],
            'is_blood_donor' => ['nullable', 'boolean'],
            'allergies' => ['nullable', 'string', 'max:1000'],
            'medical_notes' => ['nullable', 'string', 'max:1000'],
            'public_notes' => ['nullable', 'string', 'max:1000'],
            'vehicle' => ['nullable', 'string', 'max:120'],
            'motorcycle_plate' => ['nullable', 'string', 'max:30'],
            'has_vehicle_insurance' => ['nullable', 'boolean'],
            'vehicle_insurance_policy' => ['nullable', 'required_if:has_vehicle_insurance,1', 'string', 'max:120'],
            'vehicle_insurance_expires_at' => ['nullable', 'required_if:has_vehicle_insurance,1', 'date'],
            'has_public_health_insurance' => ['nullable', 'boolean'],
            'public_health_provider' => ['nullable', 'required_if:has_public_health_insurance,1', Rule::in(array_keys(SmartTag::publicHealthProviders()))],
            'public_health_number' => ['nullable', 'required_if:has_public_health_insurance,1', 'string', 'max:120'],
            'club_name' => ['nullable', 'string', 'max:120'],
            'pet_species' => ['nullable', 'string', 'max:80'],
            'pet_breed' => ['nullable', 'string', 'max:120'],
            'vet_name' => ['nullable', 'string', 'max:120'],
            'vet_phone' => ['nullable', 'string', 'max:30'],
            'vet_email' => ['nullable', 'email', 'max:160'],
            'expires_at' => ['nullable', 'date'],
        ]);

        $data['is_active'] = $request->boolean('is_active');
        $data['is_blood_donor'] = $request->boolean('is_blood_donor');
        $data['has_vehicle_insurance'] = $request->boolean('has_vehicle_insurance');
        $data['has_public_health_insurance'] = $request->boolean('has_public_health_insurance');

        if ($data['type'] !== SmartTag::TYPE_BIKER || ! $data['has_vehicle_insurance']) {
            $data['has_vehicle_insurance'] = false;
            $data['vehicle_insurance_policy'] = null;
            $data['vehicle_insurance_expires_at'] = null;
        }

        if ($data['type'] !== SmartTag::TYPE_BIKER || ! $data['has_public_health_insurance']) {
            $data['has_public_health_insurance'] = false;
            $data['public_health_provider'] = null;
            $data['public_health_number'] = null;
        }

        return $data;
    }
}
