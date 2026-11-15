<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Clients') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <form method="GET" action="{{ route('panel.clients.index') }}" class="bg-white shadow sm:rounded-lg p-6 grid gap-4 sm:grid-cols-3 items-end">
                <x-form.input type="number" name="min_spent" :label="__('Minimum spent (€)')" :value="request('min_spent')" min="0" step="0.01" />
                <div>
                    <x-button>{{ __('Filter') }}</x-button>
                </div>
            </form>

            <div class="bg-white shadow sm:rounded-lg overflow-hidden">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-left text-gray-600">
                        <tr>
                            <th class="px-4 py-3">{{ __('Client') }}</th>
                            <th class="px-4 py-3">{{ __('Email') }}</th>
                            <th class="px-4 py-3 text-right">{{ __('Total spent') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse ($clients as $client)
                            <tr>
                                <td class="px-4 py-3">{{ $client->full_name }}</td>
                                <td class="px-4 py-3">{{ $client->user->email }}</td>
                                <td class="px-4 py-3 text-right"><x-price :amount="$client->total_spent ?? 0" /></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-4 py-6 text-center text-gray-500">{{ __('No clients match the filter.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $clients->links() }}
        </div>
    </div>
</x-app-layout>
