<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckoutCartRequest;
use App\Http\Requests\StoreOrderRequest;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function preview(CheckoutCartRequest $request): RedirectResponse
    {
        $lines = $this->resolveItems($request->validated('items'));
        $request->session()->put('checkout', [
            'token' => (string) Str::uuid(),
            'items' => $lines->map(fn (array $line): array => [
                'product_id' => $line['product']->id,
                'quantity' => $line['quantity'],
                'price' => $line['product']->price,
            ])->all(),
        ]);

        return redirect()->route('checkout.create');
    }

    public function create(Request $request): View|RedirectResponse
    {
        $checkout = $request->session()->get('checkout');
        if (! $checkout) {
            return redirect()->route('products.index')->with('status', 'Pilih produk dan buka keranjang untuk checkout.');
        }
        if (isset($checkout['reference'])) {
            return redirect()->route('checkout.success', $checkout['reference']);
        }
        try {
            $lines = $this->resolveItems($checkout['items']);
        } catch (ValidationException $exception) {
            return redirect()->route('products.index')->withErrors($exception->errors());
        }
        foreach ($lines as $index => $line) {
            $checkout['items'][$index]['price'] = $line['product']->price;
        }
        $request->session()->put('checkout', $checkout);

        return view('checkout.create', [
            'lines' => $lines,
            'total' => $lines->sum(fn (array $line): int => $line['product']->price * $line['quantity']),
            'token' => $checkout['token'],
        ]);
    }

    public function store(StoreOrderRequest $request): RedirectResponse
    {
        $checkout = $request->session()->get('checkout');
        if (! $checkout || $checkout['token'] !== $request->validated('checkout_token')) {
            throw ValidationException::withMessages(['checkout' => 'Checkout sudah berubah. Buka keranjang dan ulangi checkout.']);
        }
        try {
            $order = DB::transaction(function () use ($checkout, $request): Order {
                $existing = Order::where('checkout_token', $checkout['token'])->first();
                if ($existing) {
                    return $existing;
                }
                $lines = $this->resolveItems($checkout['items'], true);
                foreach ($lines as $index => $line) {
                    if ((int) $line['product']->price !== (int) $checkout['items'][$index]['price']) {
                        throw ValidationException::withMessages(['checkout' => 'Harga produk berubah. Muat ulang halaman checkout untuk melihat harga terbaru.']);
                    }
                }
                $order = Order::create([
                    ...$request->safe()->only(['customer_name', 'phone', 'address', 'notes']),
                    'reference' => 'CS-'.strtoupper(Str::random(10)),
                    'checkout_token' => $checkout['token'],
                    'user_id' => $request->user('customer')->id,
                    'total' => $lines->sum(fn (array $line): int => $line['product']->price * $line['quantity']),
                ]);
                foreach ($lines as $line) {
                    $product = $line['product'];
                    $quantity = $line['quantity'];
                    if (Product::whereKey($product->id)->where('stock', '>=', $quantity)->decrement('stock', $quantity) !== 1) {
                        throw ValidationException::withMessages(['checkout' => 'Stok berubah. Periksa kembali keranjang Anda.']);
                    }
                    $order->items()->create([
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'unit_price' => $product->price,
                        'quantity' => $quantity,
                        'subtotal' => $product->price * $quantity,
                    ]);
                }

                return $order;
            }, 3);
        } catch (UniqueConstraintViolationException $exception) {
            $order = Order::where('checkout_token', $checkout['token'])->first();
            if (! $order) {
                throw $exception;
            }
        }
        abort_unless((int) $order->user_id === (int) $request->user('customer')->id, 404);
        $request->session()->put('checkout.reference', $order->reference);

        return redirect()->route('checkout.success', $order->reference)->with('purchased_items', $checkout['items']);
    }

    public function success(Request $request, Order $order): View
    {
        abort_unless((int) $order->user_id === (int) $request->user('customer')->id, 404);

        return view('checkout.success', ['order' => $order->load('items')]);
    }

    private function resolveItems(array $items, bool $lock = false): Collection
    {
        $query = Product::whereIn('id', array_column($items, 'product_id'))->orderBy('id');
        if ($lock) {
            $query->lockForUpdate();
        }
        $products = $query->get()->keyBy('id');

        return collect($items)->map(function (array $item) use ($products): array {
            $product = $products->get($item['product_id']);
            if (! $product || $product->stock < $item['quantity']) {
                throw ValidationException::withMessages(['checkout' => $product ? 'Stok '.$product->name.' tidak mencukupi. Perbarui jumlah di keranjang.' : 'Ada produk yang sudah tidak tersedia. Hapus produk tersebut dari keranjang.']);
            }

            return ['product' => $product, 'quantity' => (int) $item['quantity']];
        });
    }
}
