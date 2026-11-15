<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ClientController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Client::class);

        $validated = $request->validate([
            'min_spent' => ['nullable', 'numeric', 'min:0'],
        ]);

        $clients = Client::with('user')
            ->withTotalSpent()
            ->when($validated['min_spent'] ?? null, fn ($query, $min) => $query->spentAtLeast((float) $min))
            ->orderByDesc('total_spent')
            ->paginate(20)
            ->withQueryString();

        return view('panel.clients.index', ['clients' => $clients]);
    }
}
