@props(['product'])

<a href="{{ route('products.show', $product) }}" class="block bg-white rounded-lg shadow hover:shadow-md p-4">
    <p class="text-xs text-gray-500">{{ $product->category->name }} · {{ $product->brand }}</p>
    <h3 class="mt-1 font-semibold text-gray-800">{{ $product->name }}</h3>
    <div class="mt-2">
        @if ($product->discount_price)
            <x-price :amount="$product->price" class="text-sm text-gray-400 line-through" />
        @endif
        <x-price :amount="$product->final_price" class="font-semibold text-indigo-600" />
    </div>
</a>
