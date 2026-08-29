<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SalesReportController extends Controller
{
    public function index(Request $request): View
    {
        $mode = $request->query('mode') === 'week' ? 'week' : 'month';
        $date = Carbon::parse($request->query('date', now()->toDateString()));
        $start = $mode === 'week' ? $date->copy()->startOfWeek() : $date->copy()->startOfMonth();
        $end = $mode === 'week' ? $date->copy()->endOfWeek() : $date->copy()->endOfMonth();

        $orders = Order::query()
            ->with(['customer', 'items.product', 'items.bundle', 'items.salePackage'])
            ->whereNull('archived_at')
            ->where('status', '!=', 'cancelled')
            ->whereBetween('ordered_at', [$start->toDateString(), $end->toDateString()])
            ->orderByDesc('ordered_at')
            ->get();

        $productRows = [];

        foreach ($orders as $order) {
            foreach ($order->items as $item) {
                $expense = $this->itemExpense($item);
                $key = $item->item_type.':'.($item->catalog_bundle_id ?: $item->catalog_product_id ?: $item->product_name);

                if (! isset($productRows[$key])) {
                    $productRows[$key] = [
                        'name' => $item->product_name,
                        'type' => $item->item_type === 'bundle' ? 'Paquete' : 'Producto',
                        'quantity' => 0,
                        'revenue' => 0,
                        'expense' => 0,
                    ];
                }

                $productRows[$key]['quantity'] += (int) $item->quantity;
                $productRows[$key]['revenue'] += (float) $item->line_total;
                $productRows[$key]['expense'] += $expense;
            }
        }

        $productRows = collect($productRows)
            ->map(function (array $row) {
                $row['profit'] = $row['revenue'] - $row['expense'];
                return $row;
            })
            ->sortByDesc('revenue')
            ->values();

        $totalSold = (float) $orders->sum('total');
        $totalExpense = (float) $productRows->sum('expense');
        $totalPaid = (float) $orders->sum('advance_payment');
        $totalPending = (float) $orders->sum('balance_due');

        $dailyRows = collect();
        for ($day = $start->copy(); $day->lte($end); $day->addDay()) {
            $dailyRows->put($day->toDateString(), ['label' => $day->format($mode === 'week' ? 'D d' : 'd M'), 'sales' => 0, 'expense' => 0, 'profit' => 0]);
        }
        foreach ($orders as $order) {
            $key = $order->ordered_at->toDateString();
            if (! $dailyRows->has($key)) continue;
            $expense = (float) $order->items->sum(fn ($item) => $this->itemExpense($item));
            $row = $dailyRows->get($key);
            $row['sales'] += (float) $order->total;
            $row['expense'] += $expense;
            $row['profit'] += (float) $order->total - $expense;
            $dailyRows->put($key, $row);
        }

        return view('admin.reports.sales', [
            'mode' => $mode,
            'date' => $date,
            'start' => $start,
            'end' => $end,
            'orders' => $orders,
            'productRows' => $productRows,
            'summary' => [
                'orders_count' => $orders->count(),
                'completed_count' => $orders->where('balance_due', '<=', 0)->count(),
                'pending_count' => $orders->where('balance_due', '>', 0)->count(),
                'total_sold' => $totalSold,
                'total_paid' => $totalPaid,
                'total_pending' => $totalPending,
                'total_expense' => $totalExpense,
                'estimated_profit' => $totalSold - $totalExpense,
            ],
            'chartData' => [
                'daily' => $dailyRows->values(),
                'products' => $productRows->take(8)->values(),
                'payments' => ['paid' => $totalPaid, 'pending' => $totalPending],
            ],
        ]);
    }

    private function itemExpense($item): float
    {
        if ($item->item_type === 'bundle') {
            return (float) ($item->bundle?->total_cost ?? $item->bundle?->items_cost ?? 0) * (int) $item->quantity;
        }

        $unitCost = (float) ($item->product?->cost_subtotal ?? 0);

        if ($unitCost <= 0 && $item->salePackage && (int) $item->salePackage->quantity > 0) {
            $unitCost = (float) $item->salePackage->total_cost / (int) $item->salePackage->quantity;
        }

        return $unitCost * (int) $item->quantity;
    }
}
