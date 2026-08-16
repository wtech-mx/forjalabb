<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->query('status');
        $search = trim((string) $request->query('q'));

        return view('admin.customers.index', [
            'customers' => Customer::withCount('orders')
                ->when($status === 'prospects', fn ($q) => $q->where('lead_source', 'website_popup')->whereNotIn('lead_status', ['converted', 'discarded']))
                ->when($status && array_key_exists($status, Customer::LEAD_STATUSES), fn ($q) => $q->where('lead_status', $status))
                ->when($search, fn ($q) => $q->where(fn ($sub) => $sub->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%")->orWhere('phone', 'like', "%{$search}%")->orWhere('company', 'like', "%{$search}%")))
                ->latest()->paginate(20)->withQueryString(),
            'pendingCount' => Customer::where('lead_source', 'website_popup')->where('lead_status', 'pending')->count(),
            'statuses' => Customer::LEAD_STATUSES,
            'status' => $status,
            'search' => $search,
        ]);
    }

    public function update(Request $request, Customer $customer): RedirectResponse
    {
        $data = $request->validate(['lead_status' => ['required', Rule::in(array_keys(Customer::LEAD_STATUSES))]]);
        $customer->update($data + ['contacted_at' => $data['lead_status'] === 'contacted' ? now() : $customer->contacted_at]);

        return back()->with('status', 'Estado del cliente actualizado.');
    }
}
