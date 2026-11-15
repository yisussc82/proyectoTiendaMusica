<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Order') }} {{ $order->order_number }}</h2>
            @can('cancel', $order)
                <form method="POST" action="{{ route('account.orders.cancel', $order) }}">
                    @csrf
                    <x-danger-button type="submit">{{ __('Cancel order') }}</x-danger-button>
                </form>
            @endcan
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow sm:rounded-lg p-6">
                <p class="text-sm text-gray-600">
                    {{ $order->created_at->format('d/m/Y') }} · {{ $order->status->label() }}
                </p>

                <table class="mt-4 w-full text-sm">
                    <thead class="text-left text-gray-600 border-b">
                        <tr>
                            <th class="py-2">{{ __('Product') }}</th>
                            <th class="py-2 text-right">{{ __('Price') }}</th>
                            <th class="py-2 text-right">{{ __('Quantity') }}</th>
                            <th class="py-2 text-right">{{ __('Subtotal') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @foreach ($order->items as $item)
                            <tr>
                                <td class="py-2">{{ $item->name_at_sale }}</td>
                                <td class="py-2 text-right"><x-price :amount="$item->price_at_sale" /></td>
                                <td class="py-2 text-right">{{ $item->quantity }}</td>
                                <td class="py-2 text-right"><x-price :amount="$item->subtotal()" /></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="mt-4 text-right space-y-1">
                    <p class="text-sm text-gray-600">{{ __('Shipping') }}: <x-price :amount="$order->shipping_cost" /></p>
                    <p class="font-semibold">{{ __('Total') }}: <x-price :amount="$order->total" /></p>
                </div>

                <p class="mt-6 text-sm text-gray-600">
                    {{ __('Shipping address') }}: {{ $order->shipping_address_line_1 }}, {{ $order->shipping_postal_code }} {{ $order->shipping_city }} ({{ $order->shipping_province }})
                </p>
            </div>
        </div>
    </div>
</x-app-layout>
