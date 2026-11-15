<?php

namespace App\Http\Controllers\Panel;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\OrderFilterRequest;
use App\Http\Requests\UpdateOrderStatusRequest;
use App\Models\Order;
use App\Services\OrderStatusService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use LogicException;

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
            ->mapWithKeys(fn (OrderStatus $status) => [$status->value => $status->label()])
            ->all();

        return view('panel.orders.index', [
            'orders' => $orders,
            'statuses' => $statuses,
        ]);
    }

    public function updateStatus(UpdateOrderStatusRequest $request, Order $order, OrderStatusService $orderStatus): RedirectResponse
    {
        return $this->applyChange($order, OrderStatus::from($request->validated('status')), $orderStatus);
    }

    public function cancel(Order $order, OrderStatusService $orderStatus): RedirectResponse
    {
        Gate::authorize('cancel', $order);

        return $this->applyChange($order, OrderStatus::Cancelled, $orderStatus);
    }

    private function applyChange(Order $order, OrderStatus $status, OrderStatusService $orderStatus): RedirectResponse
    {
        try {
            $orderStatus->change($order, $status);
        } catch (LogicException $exception) {
            return back()
                ->with('flash.banner', $exception->getMessage())
                ->with('flash.bannerStyle', 'danger');
        }

        return back()->with('flash.banner', __('Order status updated.'));
    }
}
