<?php

namespace App\Http\Controllers\Account;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OrderStatusService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        return view('account.orders.index', [
            'orders' => $request->user()->orders()->latest()->paginate(10),
        ]);
    }

    public function show(Order $order): View
    {
        Gate::authorize('view', $order);

        $order->load('items');

        return view('account.orders.show', ['order' => $order]);
    }

    public function cancel(Order $order, OrderStatusService $orderStatus): RedirectResponse
    {
        Gate::authorize('cancel', $order);

        $orderStatus->change($order, OrderStatus::Cancelled);

        return redirect()->route('account.orders.show', $order)
            ->with('flash.banner', __('Order cancelled.'));
    }
}
