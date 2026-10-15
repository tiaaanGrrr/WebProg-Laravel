<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class CustomerOrderHistoryTest extends TestCase
{
    use LazilyRefreshDatabase;

    private function buyer(): User
    {
        $buyer = User::factory()->create();
        $this->actingAs($buyer, 'customer');

        return $buyer;
    }

    public function test_empty_history_does_not_expose_other_orders(): void
    {
        $other = Order::factory()->create();
        $this->get('/orders')->assertRedirect('/login');
        $this->buyer();

        $this->get('/orders?reference='.$other->reference)->assertOk()->assertSee('Belum Ada Pesanan di Sini')->assertDontSee($other->reference);
        $this->get('/orders/'.$other->reference)->assertNotFound();
    }

    public function test_history_and_detail_only_show_owned_orders_with_current_status(): void
    {
        $buyer = $this->buyer();
        $own = Order::factory()->for($buyer)->create();
        $other = Order::factory()->create();
        $own->status = 'dibatalkan';
        $own->save();

        $this->get('/orders')->assertOk()->assertSee($own->reference)->assertSee('Dibatalkan')->assertDontSee($other->reference);
        $this->get('/orders/'.$own->reference)->assertOk()->assertSee('Dibatalkan')->assertSee('CETAK RINGKASAN')->assertDontSee('data-purchased-items', false);
        $this->get('/orders/'.$other->reference)->assertNotFound();
        $this->get('/orders/CS-NOT-FOUND')->assertNotFound();
    }

    public function test_checkout_automatically_adds_order_to_history(): void
    {
        $this->buyer();
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
        $buyer = $this->buyer();
        $owned = Order::factory()->for($buyer)->count(12)->create();
        $other = Order::factory()->create();

        $this->get('/orders')->assertOk()->assertSee('Selanjutnya')
            ->assertDontSee($other->reference)->assertViewHas('orders', fn ($orders): bool => $orders->total() === 12 && $orders->count() === 10);
        $this->get('/orders?page=2')->assertOk()->assertViewHas('orders', fn ($orders): bool => $orders->getCollection()->modelKeys() === [$owned[1]->id, $owned[0]->id]);
    }

    public function test_detail_preserves_deleted_product_snapshot_and_escapes_content(): void
    {
        $buyer = $this->buyer();
        $item = OrderItem::factory()->create(['product_name' => '<script>alert(1)</script>']);
        $order = $item->order;
        $order->user_id = $buyer->id;
        $order->address = '<img src=x onerror=alert(2)>';
        $order->save();
        Product::findOrFail($item->product_id)->delete();

        $this->get('/orders/'.$order->reference)->assertOk()
            ->assertSee($item->product_name)->assertDontSee($item->product_name, false)
            ->assertSee($order->address)->assertDontSee($order->address, false);
    }

    public function test_history_links_and_print_action_are_available(): void
    {
        $this->get('/')->assertOk()->assertSee(route('orders.index'));
        $buyer = $this->buyer();
        $order = Order::factory()->for($buyer)->create();

        $this->get(route('checkout.success', $order->reference))->assertOk()->assertSee('data-print-order', false)->assertSee(route('orders.index'));
        $this->getJson('/orders?page=0')->assertUnprocessable()->assertJsonValidationErrors('page');
    }
}
