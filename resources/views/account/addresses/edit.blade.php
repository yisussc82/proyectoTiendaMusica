<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Edit address') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-xl sm:rounded-lg p-6">
                <form method="POST" action="{{ route('account.addresses.update', $address) }}">
                    @csrf
                    @method('PUT')
                    @include('account.addresses.form')
                    <div class="mt-6 flex items-center justify-end gap-4">
                        <a href="{{ route('account.addresses.index') }}" class="text-sm text-gray-600 hover:underline">{{ __('Cancel') }}</a>
                        <x-button>{{ __('Save') }}</x-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
