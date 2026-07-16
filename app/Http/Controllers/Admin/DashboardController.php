<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SmartTag;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('admin.dashboard', [
            'totalTags' => SmartTag::count(),
            'activeTags' => SmartTag::where('is_active', true)->count(),
            'bikerTags' => SmartTag::where('type', SmartTag::TYPE_BIKER)->count(),
            'dogTags' => SmartTag::where('type', SmartTag::TYPE_DOG)->count(),
            'latestTags' => SmartTag::latest()->take(6)->get(),
        ]);
    }
}
