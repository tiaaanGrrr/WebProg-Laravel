<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateOrderStatusRequest;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AdminOrderController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', Rule::in(array_keys(Order::STATUS_LABELS))],
            'page' => ['nullable', 'integer', 'min:1'],
        ]);
        $query = Order::withCount('items');
        if (($filters['q'] ?? '') !== '') {
            $keyword = '%'.$filters['q'].'%';
            $query->where(fn (Builder $search): Builder => $search->where('reference', 'like', $keyword)->orWhere('customer_name', 'like', $keyword)->orWhere('phone', 'like', $keyword));
        }
        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return view('admin.orders.index', ['orders' => $query->latest('id')->paginate(10)->withQueryString(), 'filters' => $filters]);
    }

    public function show(Order $order): View
    {
        return view('admin.orders.show', ['order' => $order->load('items')]);
    }

    public function update(UpdateOrderStatusRequest $request, Order $order): RedirectResponse
    {
        DB::transaction(function () use ($request, $order): void {
            $current = Order::whereKey($order->id)->lockForUpdate()->firstOrFail();
            $target = $request->validated('status');
            if ($current->status === $target) {
                return;
            }
            if ($current->status !== $request->validated('current_status')) {
                throw ValidationException::withMessages(['status' => 'Status sudah berubah. Muat ulang halaman sebelum melanjutkan.']);
            }
            if (! array_key_exists($target, $current->nextStatuses())) {
                throw ValidationException::withMessages(['status' => 'Perubahan status ini tidak diperbolehkan.']);
            }
            $updated = Order::whereKey($current->id)->where('status', $current->status)->update(['status' => $target]);
            if ($updated !== 1) {
                throw ValidationException::withMessages(['status' => 'Status sudah berubah. Muat ulang halaman sebelum melanjutkan.']);
            }
            if ($target === 'dibatalkan') {
                foreach ($current->items()->whereNotNull('product_id')->orderBy('product_id')->get() as $item) {
                    Product::whereKey($item->product_id)->increment('stock', $item->quantity);
                }
            }
        }, 3);

        return redirect()->route('admin.orders.show', $order)->with('status', 'Status pesanan berhasil diperbarui.');
    }
}
