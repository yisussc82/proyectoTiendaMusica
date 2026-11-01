<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        if ($request->user()->hasRole(Role::CUSTOMER)) {
            return redirect()->route('account.dashboard');
        }

        return redirect()->route('panel.dashboard');
    }
}
