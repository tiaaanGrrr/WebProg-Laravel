<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class CustomerOrderHistoryTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_empty_history_does_not_expose_other_orders(): void
    {
        $other = Order::factory()->create();

        $this->get('/orders?reference='.$other->reference)->assertOk()->assertSee('Belum Ada Pesanan di Sini')->assertDontSee($other->reference);
        $this->get('/orders/'.$other->reference)->assertNotFound();
    }

    public function test_history_and_detail_only_show_owned_orders_with_current_status(): void
    {
        $own = Order::factory()->create();
        $other = Order::factory()->create();
        $own->status = 'dibatalkan';
        $own->save();

        $this->withSession(['order_references' => [$own->reference]])->get('/orders')->assertOk()->assertSee($own->reference)->assertSee('Dibatalkan')->assertDontSee($other->reference);
        $this->get('/orders/'.$own->reference)->assertOk()->assertSee('Dibatalkan')->assertSee('CETAK RINGKASAN')->assertDontSee('data-purchased-items', false);
        $this->get('/orders/'.$other->reference)->assertNotFound();
        $this->get('/orders/CS-NOT-FOUND')->assertNotFound();
    }

    public function test_checkout_automatically_adds_order_to_history(): void
    {
        $product = Product::factory()->create();
        $this->post('/checkout/preview', ['items' => [['product_id' => $product->id, 'quantity' => 1]]])->assertRedirect('/checkout');
        $token = session('checkout.token');

        $this->post('/checkout', ['checkout_token' => $token, 'customer_name' => 'Budi', 'phone' => '081234567890', 'address' => 'Jalan Pancoran nomor 42, Jakarta'])->assertRedirect();

        $order = Order::sole();
        $this->get('/orders')->assertOk()->assertSee($order->reference)->assertSee(route('orders.show', $order->reference));
        $this->get('/orders/'.$order->reference)->assertOk()->assertSee($product->name)->assertSee('Rp 125.000');
    }

    public function test_history_paginates_only_owned_orders(): void
    {
        $owned = Order::factory()->count(12)->create();
        $other = Order::factory()->create();

        $this->withSession(['order_references' => $owned->pluck('reference')->all()])->get('/orders')->assertOk()->assertSee('Selanjutnya')
            ->assertDontSee($other->reference)->assertViewHas('orders', fn ($orders): bool => $orders->total() === 12 && $orders->count() === 10);
        $this->get('/orders?page=2')->assertOk()->assertViewHas('orders', fn ($orders): bool => $orders->getCollection()->modelKeys() === [$owned[1]->id, $owned[0]->id]);
    }

    public function test_detail_preserves_deleted_product_snapshot_and_escapes_content(): void
    {
        $item = OrderItem::factory()->create(['product_name' => '<script>alert(1)</script>']);
        $order = $item->order;
        $order->address = '<img src=x onerror=alert(2)>';
        $order->save();
        Product::findOrFail($item->product_id)->delete();

        $this->withSession(['order_references' => [$order->reference]])->get('/orders/'.$order->reference)->assertOk()
            ->assertSee($item->product_name)->assertDontSee($item->product_name, false)
            ->assertSee($order->address)->assertDontSee($order->address, false);
    }

    public function test_history_links_and_print_action_are_available(): void
    {
        $this->get('/')->assertOk()->assertSee(route('orders.index'));
        $order = Order::factory()->create();

        $this->withSession(['order_references' => [$order->reference]])->get(route('checkout.success', $order->reference))->assertOk()->assertSee('data-print-order', false)->assertSee(route('orders.index'));
        $this->getJson('/orders?page=0')->assertUnprocessable()->assertJsonValidationErrors('page');
    }
}
