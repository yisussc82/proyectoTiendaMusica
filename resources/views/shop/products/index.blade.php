<x-shop-layout>
    <h1 class="text-2xl font-semibold">{{ __('Products') }}</h1>

    <form method="GET" action="{{ route('products.index') }}" class="mt-6 bg-white rounded-lg shadow p-4 grid gap-4 sm:grid-cols-4 items-end">
        <x-form.input name="search" :label="__('Search')" :value="request('search')" />
        <x-form.select name="category" :label="__('Category')" :options="$categories" :selected="request('category')" :placeholder="__('All')" />
        <div class="space-y-2">
            <x-form.checkbox name="on_sale" :label="__('On sale')" :checked="request()->boolean('on_sale')" />
            <x-form.checkbox name="in_stock" :label="__('In stock')" :checked="request()->boolean('in_stock')" />
        </div>
        <div>
            <x-button>{{ __('Filter') }}</x-button>
        </div>
    </form>

    <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        @forelse ($products as $product)
            <x-product-card :product="$product" />
        @empty
            <p class="text-gray-500">{{ __('No products match the filter.') }}</p>
        @endforelse
    </div>

    <div class="mt-8">
        {{ $products->links() }}
    </div>
</x-shop-layout>
