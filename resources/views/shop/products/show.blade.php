<x-shop-layout>
    <a href="{{ route('products.index') }}" class="text-sm text-indigo-600 hover:underline">&larr; {{ __('Products') }}</a>

    <div class="mt-4 bg-white rounded-lg shadow p-6">
        <p class="text-sm text-gray-500">{{ $product->category->name }} · {{ $product->brand }} · {{ $product->sku }}</p>
        <h1 class="mt-1 text-2xl font-semibold">{{ $product->name }}</h1>

        <div class="mt-4 text-xl">
            @if ($product->discount_price)
                <x-price :amount="$product->price" class="text-gray-400 line-through me-2" />
            @endif
            <x-price :amount="$product->final_price" class="font-semibold text-indigo-600" />
        </div>

        <p class="mt-2 text-sm {{ $product->stock > 0 ? 'text-green-600' : 'text-red-600' }}">
            {{ $product->stock > 0 ? __('In stock') : __('Out of stock') }}
        </p>

        <div class="mt-6 prose max-w-none">{!! $product->description !!}</div>
    </div>
</x-shop-layout>
