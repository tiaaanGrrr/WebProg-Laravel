<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    use LazilyRefreshDatabase;

    private function receiver(): array
    {
        return ['customer_name' => 'Budi', 'phone' => '081234567890', 'address' => 'Jalan Pancoran nomor 42, Jakarta', 'notes' => 'Demo kelas'];
    }

    private function startCheckout(Product $product, int $quantity = 2): string
    {
        $this->actingAs(User::factory()->create(), 'customer');
        $this->post('/checkout/preview', ['items' => [['product_id' => $product->id, 'quantity' => $quantity, 'price' => 1]], 'total' => 1])->assertRedirect('/checkout');

        return session('checkout.token');
    }

    public function test_checkout_saves_trusted_prices_and_reduces_stock_once(): void
    {
        $product = Product::factory()->create(['stock' => 5, 'price' => 125000]);
        $token = $this->startCheckout($product);
        $this->get('/checkout')->assertOk()->assertSee('Rp 250.000');

        $response = $this->post('/checkout', [...$this->receiver(), 'checkout_token' => $token, 'total' => 1, 'items' => []]);

        $order = Order::sole();
        $response->assertRedirect(route('checkout.success', $order->reference));
        $this->assertDatabaseHas('orders', ['id' => $order->id, 'total' => 250000, 'customer_name' => 'Budi']);
        $this->assertDatabaseHas('order_items', ['order_id' => $order->id, 'product_id' => $product->id, 'unit_price' => 125000, 'quantity' => 2, 'subtotal' => 250000]);
        $this->assertDatabaseHas('products', ['id' => $product->id, 'stock' => 3]);
        $this->get(route('checkout.success', $order->reference))->assertOk()->assertSee($order->reference)->assertSee($product->name);
        $this->post('/checkout', [...$this->receiver(), 'checkout_token' => $token])->assertRedirect(route('checkout.success', $order->reference));
        $this->assertDatabaseCount('orders', 1);
        $this->assertDatabaseHas('products', ['id' => $product->id, 'stock' => 3]);
    }

    public function test_empty_duplicate_and_invalid_quantity_carts_are_rejected(): void
    {
        $product = Product::factory()->create();
        $this->get('/checkout')->assertRedirect('/login');
        $this->post('/checkout/preview', [])->assertSessionHasErrors('items');
        $this->post('/checkout/preview', ['items' => [['product_id' => $product->id, 'quantity' => 0]]])->assertSessionHasErrors('items.0.quantity');
        $this->post('/checkout/preview', ['items' => [['product_id' => $product->id, 'quantity' => 100]]])->assertSessionHasErrors('items.0.quantity');
        $this->post('/checkout/preview', ['items' => [['product_id' => $product->id, 'quantity' => 1], ['product_id' => $product->id, 'quantity' => 1]]])->assertSessionHasErrors('items.0.product_id');
        $this->assertDatabaseCount('orders', 0);
        $this->assertDatabaseHas('products', ['id' => $product->id, 'stock' => 10]);
    }

    public function test_deleted_or_insufficient_products_are_rejected_in_preview(): void
    {
        $product = Product::factory()->create(['stock' => 1]);
        $this->post('/checkout/preview', ['items' => [['product_id' => $product->id, 'quantity' => 2]]])->assertSessionHasErrors('checkout');
        $product->delete();
        $this->post('/checkout/preview', ['items' => [['product_id' => $product->id, 'quantity' => 1]]])->assertSessionHasErrors('checkout');
        $this->assertDatabaseCount('orders', 0);
    }

    public function test_stock_change_prevents_the_entire_order(): void
    {
        $this->actingAs(User::factory()->create(), 'customer');
        $first = Product::factory()->create(['stock' => 5]);
        $second = Product::factory()->create(['stock' => 5]);
        $this->post('/checkout/preview', ['items' => [['product_id' => $first->id, 'quantity' => 2], ['product_id' => $second->id, 'quantity' => 2]]])->assertRedirect('/checkout');
        $token = session('checkout.token');
        $second->update(['stock' => 1]);

        $this->post('/checkout', [...$this->receiver(), 'checkout_token' => $token])->assertSessionHasErrors('checkout');

        $this->assertDatabaseCount('orders', 0);
        $this->assertDatabaseCount('order_items', 0);
        $this->assertDatabaseHas('products', ['id' => $first->id, 'stock' => 5]);
        $this->get('/checkout')->assertRedirect('/products');
    }

    public function test_changed_prices_require_review_before_submission(): void
    {
        $product = Product::factory()->create(['price' => 125000]);
        $token = $this->startCheckout($product);
        $product->update(['price' => 150000]);

        $this->post('/checkout', [...$this->receiver(), 'checkout_token' => $token])->assertSessionHasErrors('checkout');
        $this->assertDatabaseCount('orders', 0);
        $this->get('/checkout')->assertOk()->assertSee('Rp 300.000');
        $this->post('/checkout', [...$this->receiver(), 'checkout_token' => $token])->assertSessionHasNoErrors();
        $this->assertDatabaseHas('orders', ['total' => 300000]);
    }

    public function test_receiver_and_checkout_token_are_validated(): void
    {
        $product = Product::factory()->create();
        $token = $this->startCheckout($product);
        $this->post('/checkout', ['checkout_token' => $token])->assertSessionHasErrors(['customer_name', 'phone', 'address']);
        $this->post('/checkout', [...$this->receiver(), 'phone' => 'abc', 'checkout_token' => $token])->assertSessionHasErrors('phone');
        $this->post('/checkout', [...$this->receiver(), 'checkout_token' => (string) Str::uuid()])->assertSessionHasErrors('checkout');
        $this->assertDatabaseCount('orders', 0);
    }

    public function test_receipts_are_private_and_admin_can_view_order_snapshots(): void
    {
        $item = OrderItem::factory()->create(['product_name' => '<script>alert(1)</script>']);
        $order = $item->order;
        Product::findOrFail($item->product_id)->delete();
        $this->get(route('checkout.success', $order->reference))->assertRedirect('/login');
        $buyer = User::factory()->create();
        $order->user_id = $buyer->id;
        $order->save();
        $this->actingAs($buyer, 'customer')->get(route('checkout.success', $order->reference))->assertOk()->assertSee($item->product_name)->assertDontSee($item->product_name, false);
        $this->get('/admin/orders')->assertRedirect('/admin/login');
        $user = User::factory()->create();
        $this->actingAs($user, 'web')->get('/admin/orders/'.$order->id)->assertForbidden();
        $user->is_admin = true;
        $user->save();
        $this->get('/admin/orders')->assertOk()->assertSee($order->reference);
        $this->get('/admin/orders/'.$order->id)->assertOk()->assertSee($item->product_name);
        $this->assertDatabaseHas('order_items', ['id' => $item->id, 'product_id' => null, 'product_name' => $item->product_name]);
    }
}
