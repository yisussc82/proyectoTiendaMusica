<?php

namespace App\Http\Controllers\Panel;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\OrderFilterRequest;
use App\Models\Order;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(OrderFilterRequest $request): View
    {
        Gate::authorize('viewAny', Order::class);

        $orders = Order::with('user')
            ->when($request->validated('status'), fn ($query, $status) => $query->where('status', $status))
            ->when($request->validated('from'), fn ($query, $from) => $query->whereDate('created_at', '>=', $from))
            ->when($request->validated('to'), fn ($query, $to) => $query->whereDate('created_at', '<=', $to))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $statuses = collect(OrderStatus::cases())
            ->mapWithKeys(fn (OrderStatus $status) => [$status->value => __(ucfirst($status->value))])
            ->all();

        return view('panel.orders.index', [
            'orders' => $orders,
            'statuses' => $statuses,
        ]);
    }
}
