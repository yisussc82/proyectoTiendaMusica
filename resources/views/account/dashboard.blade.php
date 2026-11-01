<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('My account') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-xl sm:rounded-lg p-6">
                <p>{{ __('Hello') }}, {{ auth()->user()->client->full_name }}.</p>
                <div class="mt-4 flex gap-6">
                    <a href="{{ route('account.addresses.index') }}" class="text-indigo-600 hover:underline">{{ __('My addresses') }}</a>
                    <a href="{{ route('account.orders.index') }}" class="text-indigo-600 hover:underline">{{ __('My orders') }}</a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
