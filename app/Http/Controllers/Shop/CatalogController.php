<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\View\View;

class CatalogController extends Controller
{
    public function home(): View
    {
        $featured = Product::where('is_active', true)
            ->where('is_featured', true)
            ->with('category')
            ->latest()
            ->take(8)
            ->get();

        return view('shop.home', ['featured' => $featured]);
    }

    public function index(): View
    {
        $products = Product::where('is_active', true)
            ->with('category')
            ->orderBy('name')
            ->paginate(12);

        return view('shop.products.index', ['products' => $products]);
    }

    public function show(Product $product): View
    {
        abort_unless($product->is_active, 404);

        $product->load('category');

        return view('shop.products.show', ['product' => $product]);
    }
}
