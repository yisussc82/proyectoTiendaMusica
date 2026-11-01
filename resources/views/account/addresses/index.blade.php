<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('My addresses') }}</h2>
            @can('create', App\Models\ClientAddress::class)
                <a href="{{ route('account.addresses.create') }}">
                    <x-secondary-button>{{ __('New address') }}</x-secondary-button>
                </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @forelse ($addresses as $address)
                <div class="bg-white shadow sm:rounded-lg p-6 flex items-start justify-between">
                    <div>
                        <p class="font-medium">
                            {{ $address->address_line_1 }}
                            @if ($address->is_default)
                                <span class="ms-2 text-xs bg-indigo-100 text-indigo-700 rounded px-2 py-0.5">{{ __('Default') }}</span>
                            @endif
                        </p>
                        @if ($address->address_line_2)
                            <p class="text-sm text-gray-600">{{ $address->address_line_2 }}</p>
                        @endif
                        <p class="text-sm text-gray-600">{{ $address->postal_code }} {{ $address->city }} ({{ $address->province }})</p>
                    </div>
                    <div class="flex items-center gap-3">
                        @can('update', $address)
                            <a href="{{ route('account.addresses.edit', $address) }}" class="text-sm text-indigo-600 hover:underline">{{ __('Edit') }}</a>
                        @endcan
                        @can('delete', $address)
                            <form method="POST" action="{{ route('account.addresses.destroy', $address) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-sm text-red-600 hover:underline">{{ __('Delete') }}</button>
                            </form>
                        @endcan
                    </div>
                </div>
            @empty
                <p class="text-gray-500">{{ __('You have no addresses yet.') }}</p>
            @endforelse
        </div>
    </div>
</x-app-layout>
