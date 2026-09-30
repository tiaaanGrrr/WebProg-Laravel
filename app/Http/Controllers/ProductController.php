<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        // Ambil produk yang stoknya > 0, urutkan dari yang terbaru
        $products = Product::where('stock', '>', 0)
                            ->latest()
                            ->get();

        // Kirim data ke view resources/views/products/index.blade.php
        return view('products.index', compact('products'));
    }
}
