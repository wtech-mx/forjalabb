<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->query('status');
        $type = $request->query('type');

        if ($status === 'prospects') {
            $type = 'prospects';
            $status = null;
        }
        $search = trim((string) $request->query('q'));

        return view('admin.customers.index', [
            'customers' => Customer::withCount('orders')
                ->when($type === 'prospects', fn ($q) => $q->where('lead_source', 'website_popup')->whereNotIn('lead_status', ['converted', 'discarded']))
                ->when($type === 'customers', fn ($q) => $q->where(fn ($sub) => $sub->whereNull('lead_source')->orWhere('lead_source', '!=', 'website_popup')))
                ->when($status && array_key_exists($status, Customer::LEAD_STATUSES), fn ($q) => $q->where('lead_status', $status))
                ->when($search, fn ($q) => $q->where(fn ($sub) => $sub->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%")->orWhere('phone', 'like', "%{$search}%")->orWhere('company', 'like', "%{$search}%")))
                ->orderBy('name')->paginate(20)->withQueryString(),
            'pendingCount' => Customer::where('lead_source', 'website_popup')->where('lead_status', 'pending')->count(),
            'statuses' => Customer::LEAD_STATUSES,
            'status' => $status,
            'type' => $type,
            'search' => $search,
        ]);
    }

    public function update(Request $request, Customer $customer): RedirectResponse|JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:160'],
            'company' => ['nullable', 'string', 'max:160'],
            'phone' => ['nullable', 'string', 'max:30'],
            'whatsapp' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:160'],
            'address' => ['nullable', 'string', 'max:1000'],
            'interested_service' => ['nullable', Rule::in(['biker_tag', 'dog_tag', 'sublimation', 'laser', 'catalog', 'corporate', 'other'])],
            'lead_status' => ['required', Rule::in(array_keys(Customer::LEAD_STATUSES))],
            'notes' => ['nullable', 'string', 'max:3000'],
        ]);
        $customer->update($data + ['contacted_at' => $data['lead_status'] === 'contacted' ? now() : $customer->contacted_at]);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Cliente actualizado correctamente.',
                'customer' => $customer->fresh(),
            ]);
        }

        return back()->with('status', 'Cliente actualizado correctamente.');
    }
}
