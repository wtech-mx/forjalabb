<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LeadController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:160'],
            'email' => ['required', 'email:rfc', 'max:160'],
            'phone' => ['required', 'string', 'max:30'],
            'whatsapp' => ['required', 'string', 'max:30'],
            'company' => ['nullable', 'string', 'max:160'],
            'interested_service' => ['required', Rule::in(['biker_tag', 'dog_tag', 'sublimation', 'laser', 'catalog', 'corporate', 'other'])],
        ]);

        $customer = Customer::firstOrNew(['email' => $data['email']]);
        $customer->fill($data + [
            'lead_source' => 'website_popup',
            'lead_status' => $customer->exists && $customer->lead_status !== 'pending' ? $customer->lead_status : 'pending',
            'notes' => trim(($customer->notes ? $customer->notes."\n" : '').'Solicitó cupón de 10% para primera compra desde el sitio web.'),
        ])->save();

        return response()->json([
            'message' => '¡Listo! Registramos tus datos. Te contactaremos para aplicar tu 10% de descuento.',
        ], $customer->wasRecentlyCreated ? 201 : 200);
    }
}
