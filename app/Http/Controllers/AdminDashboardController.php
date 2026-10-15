<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(): View
    {
        $statusCounts = Order::select('status')->selectRaw('COUNT(*) as total')->groupBy('status')->pluck('total', 'status');

        return view('admin.dashboard', [
            'productCount' => Product::count(),
            'orderCount' => (int) $statusCounts->sum(),
            'statusCounts' => collect(Order::STATUS_LABELS)->map(fn (string $label, string $status): int => (int) $statusCounts->get($status, 0)),
            'lowStockCount' => Product::whereBetween('stock', [1, 5])->count(),
            'outOfStockCount' => Product::where('stock', 0)->count(),
            'completedValue' => (int) Order::where('status', 'selesai')->sum('total'),
            'lowStockProducts' => Product::where('stock', '<=', 5)->orderBy('stock')->orderBy('name')->limit(5)->get(),
            'latestOrders' => Order::latest('id')->limit(5)->get(),
        ]);
    }
}
