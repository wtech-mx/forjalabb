<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ExpenseController extends Controller
{
    public function index(Request $request): View
    {
        $monthInput = (string) $request->query('month', now()->format('Y-m'));
        try {
            $month = Carbon::parse(strlen($monthInput) === 7 ? $monthInput.'-01' : $monthInput);
        } catch (\Throwable) {
            $month = now();
        }
        $start = $month->copy()->startOfMonth();
        $end = $month->copy()->endOfMonth();

        $expenses = Expense::query()
            ->with('creator')
            ->whereBetween('spent_at', [$start->toDateString(), $end->toDateString()])
            ->orderByDesc('spent_at')
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.expenses.index', [
            'expenses' => $expenses,
            'month' => $month,
            'start' => $start,
            'end' => $end,
            'total' => Expense::query()
                ->whereBetween('spent_at', [$start->toDateString(), $end->toDateString()])
                ->sum('amount'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'spent_at' => ['required', 'date'],
            'category' => ['nullable', 'string', 'max:80'],
            'concept' => ['required', 'string', 'max:160'],
            'amount' => ['required', 'numeric', 'min:0.01', 'max:9999999.99'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $data['created_by'] = $request->user()->id;
        $data['category'] = $data['category'] ?: 'General';

        Expense::create($data);

        return redirect()
            ->route('admin.expenses.index', ['month' => $data['spent_at']])
            ->with('status', 'Gasto registrado correctamente.');
    }

    public function destroy(Expense $expense): RedirectResponse
    {
        $expense->delete();

        return back()->with('status', 'Gasto eliminado.');
    }
}
