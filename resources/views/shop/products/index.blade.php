<x-shop-layout>
    <h1 class="text-2xl font-semibold">{{ __('Products') }}</h1>

    <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        @foreach ($products as $product)
            <x-product-card :product="$product" />
        @endforeach
    </div>

    <div class="mt-8">
        {{ $products->links() }}
    </div>
</x-shop-layout>
