<?php

namespace App\Http\Controllers;

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

class PublicTagIntakeController extends Controller
{
    public function edit(string $token): View
    {
        $tag = SmartTag::where('intake_token', $token)->firstOrFail();

        return view('tags.intake', compact('tag'));
    }

    public function update(Request $request, string $token): RedirectResponse
    {
        $tag = SmartTag::where('intake_token', $token)->firstOrFail();
        $data = $request->validate([
            'display_name' => ['required', 'string', 'max:120'],
            'owner_name' => ['required', 'string', 'max:120'],
            'owner_phone' => ['required', 'string', 'max:30'],
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
            'club_name' => ['nullable', 'string', 'max:120'],
            'has_vehicle_insurance' => ['nullable', 'boolean'],
            'vehicle_insurance_policy' => ['nullable', 'required_if:has_vehicle_insurance,1', 'string', 'max:120'],
            'vehicle_insurance_expires_at' => ['nullable', 'required_if:has_vehicle_insurance,1', 'date'],
            'has_public_health_insurance' => ['nullable', 'boolean'],
            'public_health_provider' => ['nullable', 'required_if:has_public_health_insurance,1', Rule::in(array_keys(SmartTag::publicHealthProviders()))],
            'public_health_number' => ['nullable', 'required_if:has_public_health_insurance,1', 'string', 'max:120'],
            'pet_species' => ['nullable', 'string', 'max:80'],
            'pet_breed' => ['nullable', 'string', 'max:120'],
            'vet_name' => ['nullable', 'string', 'max:120'],
            'vet_phone' => ['nullable', 'string', 'max:30'],
            'vet_email' => ['nullable', 'email', 'max:160'],
            'payment_code' => ['nullable', 'digits:4'],
        ]);

        $data['is_blood_donor'] = $tag->type === SmartTag::TYPE_BIKER && $request->boolean('is_blood_donor');
        $data['has_vehicle_insurance'] = $tag->type === SmartTag::TYPE_BIKER && $request->boolean('has_vehicle_insurance');
        $data['has_public_health_insurance'] = $tag->type === SmartTag::TYPE_BIKER && $request->boolean('has_public_health_insurance');
        if (! $data['has_vehicle_insurance']) {
            $data['vehicle_insurance_policy'] = $data['vehicle_insurance_expires_at'] = null;
        }
        if (! $data['has_public_health_insurance']) {
            $data['public_health_provider'] = $data['public_health_number'] = null;
        }
        if ($tag->type === SmartTag::TYPE_DOG) {
            foreach (['blood_type', 'vehicle', 'motorcycle_plate', 'club_name'] as $field) $data[$field] = null;
        } else {
            foreach (['pet_species', 'pet_breed', 'vet_name', 'vet_phone', 'vet_email'] as $field) $data[$field] = null;
        }

        $paymentCode = (string) ($data['payment_code'] ?? '');
        unset($data['payment_code']);
        $paid = $tag->is_active || ($paymentCode !== '' && hash_equals((string) $tag->payment_code, $paymentCode));
        $data['is_active'] = $paid;
        $data['intake_status'] = $paid ? 'active' : 'pending_payment';
        $data['client_submitted_at'] = now();
        if ($paid && ! $tag->activated_at) $data['activated_at'] = now();
        $tag->update($data);

        return back()->with('intake_result', $paid ? 'activated' : 'pending');
    }

    public function qr(string $token): Response
    {
        $tag = SmartTag::where('intake_token', $token)->firstOrFail();
        abort_unless($tag->is_active, 404);
        $renderer = new ImageRenderer(new RendererStyle(420), new SvgImageBackEnd);
        $svg = (new Writer($renderer))->writeString($tag->public_url);

        return response($svg, 200, ['Content-Type' => 'image/svg+xml']);
    }
}
