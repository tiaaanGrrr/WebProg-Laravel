<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(): View
    {
        // Ambil produk yang stoknya > 0, urutkan dari yang terbaru
        $products = Product::where('stock', '>', 0)
            ->latest()
            ->get();

        // Kirim data ke view resources/views/products/index.blade.php
        return view('products.index', compact('products'));
    }

    public function show(Product $product): View
    {
        return view('products.show', [
            'product' => $product,
            'relatedProducts' => Product::where('category', $product->category)
                ->whereKeyNot($product->id)->where('stock', '>', 0)->latest('id')->limit(4)->get(),
        ]);
    }
}
