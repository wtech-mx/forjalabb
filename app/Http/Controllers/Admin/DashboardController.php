<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AnalyticsEvent;
use App\Models\CatalogProduct;
use App\Models\Role;
use App\Models\SmartTag;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $since = now()->subDays(30);
        $analytics = AnalyticsEvent::query()->where('occurred_at', '>=', $since);
        $dailyRaw = (clone $analytics)
            ->where('event_type', 'page_view')
            ->selectRaw('DATE(occurred_at) as day, COUNT(*) as total')
            ->groupBy('day')
            ->pluck('total', 'day');
        $dailyViews = collect(range(13, 0))->map(fn (int $daysAgo) => now()->subDays($daysAgo))->push(now())->map(fn ($day) => [
            'label' => $day->format('d/m'),
            'total' => (int) ($dailyRaw[$day->toDateString()] ?? 0),
        ]);

        return view('admin.dashboard', [
            'totalProducts' => CatalogProduct::count(),
            'activeProducts' => CatalogProduct::where('is_active', true)->count(),
            'totalUsers' => User::count(),
            'totalRoles' => Role::count(),
            'totalTags' => SmartTag::count(),
            'activeTags' => SmartTag::where('is_active', true)->count(),
            'bikerTags' => SmartTag::where('type', SmartTag::TYPE_BIKER)->count(),
            'dogTags' => SmartTag::where('type', SmartTag::TYPE_DOG)->count(),
            'latestProducts' => CatalogProduct::latest()->take(6)->get(),
            'pageViews' => (clone $analytics)->where('event_type', 'page_view')->count(),
            'uniqueVisitors' => (clone $analytics)->where('event_type', 'page_view')->distinct('session_id')->count('session_id'),
            'productClicks' => (clone $analytics)->where('event_type', 'product_click')->count(),
            'whatsappClicks' => (clone $analytics)->where('event_type', 'whatsapp_click')->count(),
            'topPages' => (clone $analytics)->where('event_type', 'page_view')->selectRaw('path, COUNT(*) as total')->groupBy('path')->orderByDesc('total')->take(8)->get(),
            'topSections' => (clone $analytics)->where('event_type', 'section_view')->whereNotNull('label')->selectRaw('label, COUNT(*) as total')->groupBy('label')->orderByDesc('total')->take(8)->get(),
            'topProducts' => (clone $analytics)->where('event_type', 'product_click')->whereNotNull('label')->selectRaw('label, COUNT(*) as total')->groupBy('label')->orderByDesc('total')->take(8)->get(),
            'devices' => (clone $analytics)->where('event_type', 'page_view')->selectRaw('device, COUNT(*) as total')->groupBy('device')->orderByDesc('total')->get(),
            'sources' => (clone $analytics)->where('event_type', 'page_view')->selectRaw("COALESCE(utm_source, 'Directo / referencia') as source, COUNT(*) as total")->groupBy('source')->orderByDesc('total')->take(6)->get(),
            'dailyViews' => $dailyViews,
        ]);
    }
}
