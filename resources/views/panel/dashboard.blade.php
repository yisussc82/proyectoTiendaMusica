<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Management panel') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white shadow-xl sm:rounded-lg p-6">
                <p>{{ __('Hello') }}, {{ auth()->user()->name }}.</p>
                <p class="mt-2 text-sm text-gray-600">
                    {{ __('Roles') }}: {{ auth()->user()->roles->pluck('name')->join(', ') }}
                </p>
                <div class="mt-4 flex gap-6">
                    @can('viewAny', App\Models\Order::class)
                        <a href="{{ route('panel.orders.index') }}" class="text-indigo-600 hover:underline">{{ __('Orders') }}</a>
                    @endcan
                    @can('viewAny', App\Models\Client::class)
                        <a href="{{ route('panel.clients.index') }}" class="text-indigo-600 hover:underline">{{ __('Clients') }}</a>
                    @endcan
                </div>
            </div>

            @if (auth()->user()->hasRole(App\Models\Role::ADMIN))
                <div class="bg-white shadow-xl sm:rounded-lg p-6">
                    <h3 class="font-semibold text-gray-800">{{ __('Maintenance') }}</h3>
                    <div class="mt-4 flex flex-wrap gap-4">
                        <form method="POST" action="{{ route('panel.maintenance.cancel-stale-orders') }}">
                            @csrf
                            <x-secondary-button type="submit">{{ __('Cancel stale orders') }}</x-secondary-button>
                        </form>
                        <form method="POST" action="{{ route('panel.maintenance.refresh-featured') }}">
                            @csrf
                            <x-secondary-button type="submit">{{ __('Refresh featured products') }}</x-secondary-button>
                        </form>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
