<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;

class AdminOrderStatusTest extends TestCase
{
    use LazilyRefreshDatabase;

    private function admin(): User
    {
        $user = User::factory()->create();
        $user->is_admin = true;
        $user->save();

        return $user;
    }

    public function test_admin_can_process_and_finish_an_order(): void
    {
        $order = Order::factory()->create();

        $this->actingAs($this->admin())->patch('/admin/orders/'.$order->id, ['status' => 'diproses', 'current_status' => 'baru'])->assertRedirect('/admin/orders/'.$order->id);
        $this->assertDatabaseHas('orders', ['id' => $order->id, 'status' => 'diproses']);
        $this->get('/admin/orders')->assertOk()->assertSee('Diproses');
        $this->patch('/admin/orders/'.$order->id, ['status' => 'selesai', 'current_status' => 'diproses'])->assertRedirect('/admin/orders/'.$order->id);
        $this->assertDatabaseHas('orders', ['id' => $order->id, 'status' => 'selesai']);
        $this->get('/admin/orders/'.$order->id)->assertOk()->assertSee('Selesai')->assertDontSee('SIMPAN STATUS');
    }

    #[TestWith(['baru'])]
    #[TestWith(['diproses'])]
    public function test_cancellation_returns_stock_once_and_keeps_order_details(string $status): void
    {
        $product = Product::factory()->create(['stock' => 8]);
        $order = Order::factory()->create(['status' => $status]);
        $item = OrderItem::factory()->for($order)->create(['product_id' => $product->id, 'quantity' => 2, 'subtotal' => 250000]);
        $payload = ['status' => 'dibatalkan', 'current_status' => $status];

        $this->actingAs($this->admin())->patch('/admin/orders/'.$order->id, $payload)->assertRedirect('/admin/orders/'.$order->id);

        $this->assertDatabaseHas('orders', ['id' => $order->id, 'status' => 'dibatalkan']);
        $this->assertDatabaseHas('products', ['id' => $product->id, 'stock' => 10]);
        $this->assertModelExists($item);
        $this->patch('/admin/orders/'.$order->id, $payload)->assertRedirect('/admin/orders/'.$order->id);
        $this->assertDatabaseHas('products', ['id' => $product->id, 'stock' => 10]);
        $this->withSession(['order_references' => [$order->reference]])->get(route('checkout.success', $order->reference))->assertOk()->assertSee('Dibatalkan');
    }

    public function test_cancellation_handles_deleted_products_and_restores_remaining_products(): void
    {
        $order = Order::factory()->create();
        $deletedItem = OrderItem::factory()->for($order)->create();
        Product::findOrFail($deletedItem->product_id)->delete();
        $remaining = OrderItem::factory()->for($order)->create();

        $this->actingAs($this->admin())->patch('/admin/orders/'.$order->id, ['status' => 'dibatalkan', 'current_status' => 'baru'])->assertRedirect('/admin/orders/'.$order->id);

        $this->assertDatabaseHas('products', ['id' => $remaining->product_id, 'stock' => 11]);
        $this->assertDatabaseHas('order_items', ['id' => $deletedItem->id, 'product_id' => null]);
        $this->assertDatabaseHas('orders', ['id' => $order->id, 'status' => 'dibatalkan']);
    }

    #[TestWith(['baru', 'selesai'])]
    #[TestWith(['diproses', 'baru'])]
    #[TestWith(['selesai', 'dibatalkan'])]
    #[TestWith(['dibatalkan', 'diproses'])]
    public function test_invalid_transitions_are_rejected(string $current, string $target): void
    {
        $order = Order::factory()->create(['status' => $current]);
        $item = OrderItem::factory()->for($order)->create();

        $this->actingAs($this->admin())->patch('/admin/orders/'.$order->id, ['status' => $target, 'current_status' => $current])->assertSessionHasErrors(['status' => 'Perubahan status ini tidak diperbolehkan.']);

        $this->assertDatabaseHas('orders', ['id' => $order->id, 'status' => $current]);
        $this->assertDatabaseHas('products', ['id' => $item->product_id, 'stock' => 10]);
    }

    public function test_stale_forms_and_unknown_status_are_rejected(): void
    {
        $order = Order::factory()->create(['status' => 'diproses']);

        $this->actingAs($this->admin())->patch('/admin/orders/'.$order->id, ['status' => 'selesai', 'current_status' => 'baru'])->assertSessionHasErrors(['status' => 'Status sudah berubah. Muat ulang halaman sebelum melanjutkan.']);
        $this->patch('/admin/orders/'.$order->id, ['status' => 'bebas', 'current_status' => 'diproses'])->assertSessionHasErrors(['status' => 'Status pesanan tidak valid.']);
        $this->patch('/admin/orders/'.$order->id, [])->assertSessionHasErrors(['status', 'current_status']);
        $this->assertDatabaseHas('orders', ['id' => $order->id, 'status' => 'diproses']);
    }

    public function test_guests_and_regular_users_cannot_change_status(): void
    {
        $order = Order::factory()->create();
        $payload = ['status' => 'dibatalkan', 'current_status' => 'baru'];

        $this->patch('/admin/orders/'.$order->id, $payload)->assertRedirect('/admin/login');
        $this->actingAs(User::factory()->create())->patch('/admin/orders/'.$order->id, $payload)->assertForbidden();

        $this->assertDatabaseHas('orders', ['id' => $order->id, 'status' => 'baru']);
    }
}
