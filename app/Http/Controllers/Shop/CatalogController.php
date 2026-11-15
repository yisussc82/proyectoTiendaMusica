<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductFilterRequest;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class CatalogController extends Controller
{
    public function home(): View
    {
        $featured = Product::active()
            ->where('is_featured', true)
            ->with('category')
            ->latest()
            ->take(8)
            ->get();

        $bestSellers = Product::active()
            ->bestSelling()
            ->with('category')
            ->take(4)
            ->get();

        return view('shop.home', [
            'featured' => $featured,
            'bestSellers' => $bestSellers,
        ]);
    }

    public function index(ProductFilterRequest $request): View
    {
        $filters = $request->validated();

        $category = filled($filters['category'] ?? null)
            ? Category::where('slug', $filters['category'])->first()
            : null;

        $products = Product::active()
            ->with('category')
            ->search($filters['search'] ?? null)
            ->when($category, fn ($query, $category) => $query->inCategoryTree($category))
            ->when($filters['on_sale'], fn ($query) => $query->onSale())
            ->when($filters['in_stock'], fn ($query) => $query->inStock())
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        return view('shop.products.index', [
            'products' => $products,
            'categories' => Category::treeOptions(),
        ]);
    }

    public function show(Product $product): View
    {
        abort_unless(Gate::allows('view', $product), 404);

        $product->load('category');

        return view('shop.products.show', ['product' => $product]);
    }
}
