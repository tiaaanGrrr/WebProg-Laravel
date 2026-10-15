<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(): View
    {
        return view('home', [
            'products' => Product::where('category', '!=', 'Paket')->where('stock', '>', 0)->latest('id')->limit(4)->get(),
            'bundles' => Product::where('category', 'Paket')->where('stock', '>', 0)->oldest('id')->limit(2)->get(),
        ]);
    }
}
