<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Management panel') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-xl sm:rounded-lg p-6">
                <p>{{ __('Hello') }}, {{ auth()->user()->name }}.</p>
                <p class="mt-2 text-sm text-gray-600">
                    {{ __('Roles') }}: {{ auth()->user()->roles->pluck('name')->join(', ') }}
                </p>
                @can('viewAny', App\Models\Order::class)
                    <a href="{{ route('panel.orders.index') }}" class="inline-block mt-4 text-indigo-600 hover:underline">{{ __('Orders') }}</a>
                @endcan
            </div>
        </div>
    </div>
</x-app-layout>
