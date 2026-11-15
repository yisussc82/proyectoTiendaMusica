<x-shop-layout>
    <h1 class="text-2xl font-semibold">{{ __('Featured products') }}</h1>

    <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        @forelse ($featured as $product)
            <x-product-card :product="$product" />
        @empty
            <p class="text-gray-500">{{ __('There are no featured products yet.') }}</p>
        @endforelse
    </div>

    @if ($bestSellers->isNotEmpty())
        <h2 class="mt-10 text-xl font-semibold">{{ __('Best sellers') }}</h2>

        <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($bestSellers as $product)
                <x-product-card :product="$product" />
            @endforeach
        </div>
    @endif

    <a href="{{ route('products.index') }}" class="inline-block mt-8 text-indigo-600 hover:underline">{{ __('See all products') }}</a>
</x-shop-layout>
