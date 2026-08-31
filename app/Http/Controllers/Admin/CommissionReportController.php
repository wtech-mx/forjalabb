<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class CommissionReportController extends Controller
{
    public function index(Request $request): View
    {
        return view('admin.reports.commissions', $this->reportData($request));
    }

    public function pdf(Request $request): Response
    {
        $data = $this->reportData($request);
        $options = new Options;
        $options->set('isRemoteEnabled', true);
        $options->set('chroot', public_path());

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml(view('admin.reports.commissions-pdf', $data)->render(), 'UTF-8');
        $dompdf->setPaper('letter');
        $dompdf->render();

        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="comisiones-'.$data['start']->format('Y-m-d').'-'.$data['end']->format('Y-m-d').'.pdf"',
        ]);
    }

    private function reportData(Request $request): array
    {
        $request->validate([
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date'],
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
        ]);

        $today = now();
        $start = Carbon::parse($request->query('start_date', $today->copy()->startOfWeek()->toDateString()))->startOfDay();
        $end = Carbon::parse($request->query('end_date', $today->copy()->endOfWeek()->toDateString()))->endOfDay();

        if ($start->gt($end)) {
            [$start, $end] = [$end->copy()->startOfDay(), $start->copy()->endOfDay()];
        }

        $viewer = $request->user();
        $canSelectSeller = $viewer->hasPermissionTo('users.manage');
        $seller = $canSelectSeller && $request->filled('user_id')
            ? User::findOrFail((int) $request->query('user_id'))
            : $viewer;

        $commissionPercentage = (float) ($seller->commission_percentage ?? 0);
        $orders = Order::query()
            ->with('customer')
            ->whereNull('archived_at')
            ->where('status', '!=', 'cancelled')
            ->where('created_by', $seller->id)
            ->whereBetween('ordered_at', [$start->toDateString(), $end->toDateString()])
            ->orderByDesc('ordered_at')
            ->get();

        $rows = $this->commissionRows($orders, $commissionPercentage);

        return [
            'start' => $start,
            'end' => $end,
            'seller' => $seller,
            'sellers' => $canSelectSeller ? User::query()->orderBy('name')->get() : collect(),
            'canSelectSeller' => $canSelectSeller,
            'commissionPercentage' => $commissionPercentage,
            'rows' => $rows,
            'summary' => [
                'orders_count' => $rows->count(),
                'total_commission' => $rows->sum('commission'),
            ],
        ];
    }

    private function commissionRows(Collection $orders, float $commissionPercentage): Collection
    {
        return $orders->map(function (Order $order) use ($commissionPercentage) {
            $shipping = (float) ($order->shipping_cost ?? 0);
            $commissionBase = max(0, (float) $order->total - $shipping);

            return [
                'order' => $order,
                'shipping' => $shipping,
                'commission_base' => $commissionBase,
                'commission' => round($commissionBase * ($commissionPercentage / 100), 2),
            ];
        });
    }
}
