<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('My orders') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow sm:rounded-lg overflow-hidden">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-left text-gray-600">
                        <tr>
                            <th class="px-4 py-3">{{ __('Order') }}</th>
                            <th class="px-4 py-3">{{ __('Date') }}</th>
                            <th class="px-4 py-3">{{ __('Status') }}</th>
                            <th class="px-4 py-3 text-right">{{ __('Total') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse ($orders as $order)
                            <tr>
                                <td class="px-4 py-3">
                                    <a href="{{ route('account.orders.show', $order) }}" class="text-indigo-600 hover:underline">{{ $order->order_number }}</a>
                                </td>
                                <td class="px-4 py-3">{{ $order->created_at->format('d/m/Y') }}</td>
                                <td class="px-4 py-3">{{ __(ucfirst($order->status->value)) }}</td>
                                <td class="px-4 py-3 text-right"><x-price :amount="$order->total" /></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-6 text-center text-gray-500">{{ __('You have no orders yet.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $orders->links() }}</div>
        </div>
    </div>
</x-app-layout>
