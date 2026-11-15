<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Orders') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <form method="GET" action="{{ route('panel.orders.index') }}" class="bg-white shadow sm:rounded-lg p-6 grid gap-4 sm:grid-cols-4 items-end">
                <x-form.select name="status" :label="__('Status')" :options="$statuses" :selected="request('status')" :placeholder="__('All')" />
                <x-form.date name="from" :label="__('From')" :value="request('from')" />
                <x-form.date name="to" :label="__('To')" :value="request('to')" />
                <div>
                    <x-button>{{ __('Filter') }}</x-button>
                </div>
            </form>

            <div class="bg-white shadow sm:rounded-lg overflow-hidden">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-left text-gray-600">
                        <tr>
                            <th class="px-4 py-3">{{ __('Order') }}</th>
                            <th class="px-4 py-3">{{ __('Customer') }}</th>
                            <th class="px-4 py-3">{{ __('Date') }}</th>
                            <th class="px-4 py-3">{{ __('Status') }}</th>
                            <th class="px-4 py-3 text-right">{{ __('Total') }}</th>
                            <th class="px-4 py-3">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse ($orders as $order)
                            <tr>
                                <td class="px-4 py-3">{{ $order->order_number }}</td>
                                <td class="px-4 py-3">{{ $order->user->name }}</td>
                                <td class="px-4 py-3">{{ $order->created_at->format('d/m/Y') }}</td>
                                <td class="px-4 py-3">{{ $order->status->label() }}</td>
                                <td class="px-4 py-3 text-right"><x-price :amount="$order->total" /></td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        @can('updateStatus', $order)
                                            @if ($order->status->nextSteps())
                                                <form method="POST" action="{{ route('panel.orders.status', $order) }}" class="flex items-center gap-2">
                                                    @csrf
                                                    @method('PATCH')
                                                    <select name="status" class="text-sm border-gray-300 rounded-md">
                                                        @foreach ($order->status->nextSteps() as $status)
                                                            <option value="{{ $status->value }}">{{ $status->label() }}</option>
                                                        @endforeach
                                                    </select>
                                                    <x-secondary-button type="submit">{{ __('Change') }}</x-secondary-button>
                                                </form>
                                            @endif
                                        @endcan
                                        @can('cancel', $order)
                                            <form method="POST" action="{{ route('panel.orders.cancel', $order) }}">
                                                @csrf
                                                <button type="submit" class="text-sm text-red-600 hover:underline">{{ __('Cancel') }}</button>
                                            </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-6 text-center text-gray-500">{{ __('No orders match the filter.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $orders->links() }}
        </div>
    </div>
</x-app-layout>
