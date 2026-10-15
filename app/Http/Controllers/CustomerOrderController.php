<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerOrderController extends Controller
{
    public function index(Request $request): View
    {
        $request->validate(['page' => ['nullable', 'integer', 'min:1']]);

        return view('orders.index', [
            'orders' => $request->user('customer')->orders()
                ->withCount('items')->latest('id')->paginate(10),
        ]);
    }

    public function show(Request $request, Order $order): View
    {
        abort_unless((int) $order->user_id === (int) $request->user('customer')->id, 404);

        return view('orders.show', ['order' => $order->load('items')]);
    }
}
