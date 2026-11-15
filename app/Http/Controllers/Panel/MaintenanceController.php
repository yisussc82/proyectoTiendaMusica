<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Artisan;

class MaintenanceController extends Controller
{
    public function cancelStaleOrders(): RedirectResponse
    {
        Artisan::call('orders:cancel-stale');

        return back()->with('flash.banner', trim(Artisan::output()));
    }

    public function refreshFeatured(): RedirectResponse
    {
        Artisan::call('catalog:refresh-featured');

        return back()->with('flash.banner', trim(Artisan::output()));
    }
}
