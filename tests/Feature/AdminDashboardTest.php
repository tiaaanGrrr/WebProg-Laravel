<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use LazilyRefreshDatabase;

    private function admin(): User
    {
        $user = User::factory()->create();
        $user->is_admin = true;
        $user->save();

        return $user;
    }

    public function test_dashboard_is_restricted_to_admins(): void
    {
        $this->get('/admin')->assertRedirect('/admin/login');
        $this->actingAs(User::factory()->create())->get('/admin')->assertForbidden();
    }

    public function test_empty_dashboard_displays_zero_totals_and_helpful_empty_states(): void
    {
        $this->actingAs($this->admin())->get('/admin')->assertOk()
            ->assertSee('Dashboard Admin')
            ->assertSee('Belum ada pesanan.')
            ->assertSee('Belum ada produk.')
            ->assertViewHas('productCount', 0)
            ->assertViewHas('orderCount', 0)
            ->assertViewHas('lowStockCount', 0)
            ->assertViewHas('outOfStockCount', 0)
            ->assertViewHas('completedValue', 0)
            ->assertViewHas('statusCounts', fn ($counts): bool => $counts->all() === ['baru' => 0, 'diproses' => 0, 'selesai' => 0, 'dibatalkan' => 0]);
    }

    public function test_dashboard_counts_stock_boundaries_and_only_completed_order_value(): void
    {
        Product::factory()->count(5)->sequence(['stock' => 0], ['stock' => 1], ['stock' => 5], ['stock' => 6], ['stock' => 10])->create();
        $orders = Order::factory()->count(6)->sequence(
            ['status' => 'baru', 'total' => 100],
            ['status' => 'baru', 'total' => 200],
            ['status' => 'diproses', 'total' => 300],
            ['status' => 'selesai', 'total' => 400],
            ['status' => 'selesai', 'total' => 500],
            ['status' => 'dibatalkan', 'total' => 7000],
        )->create();

        $this->actingAs($this->admin())->get('/admin')->assertOk()
            ->assertViewHas('productCount', 5)
            ->assertViewHas('orderCount', 6)
            ->assertViewHas('lowStockCount', 2)
            ->assertViewHas('outOfStockCount', 1)
            ->assertViewHas('completedValue', 900)
            ->assertViewHas('statusCounts', fn ($counts): bool => $counts->all() === ['baru' => 2, 'diproses' => 1, 'selesai' => 2, 'dibatalkan' => 1])
            ->assertViewHas('latestOrders', fn ($latest): bool => $latest->modelKeys() === [$orders[5]->id, $orders[4]->id, $orders[3]->id, $orders[2]->id, $orders[1]->id])
            ->assertSee('Rp 900');
    }

    public function test_low_stock_list_prioritizes_lowest_stock_and_is_limited_to_five(): void
    {
        $products = Product::factory()->count(7)->sequence(['stock' => 0], ['stock' => 1], ['stock' => 2], ['stock' => 3], ['stock' => 4], ['stock' => 5], ['stock' => 6])->create();

        $this->actingAs($this->admin())->get('/admin')->assertOk()
            ->assertViewHas('lowStockProducts', fn ($listed): bool => $listed->modelKeys() === [$products[0]->id, $products[1]->id, $products[2]->id, $products[3]->id, $products[4]->id])
            ->assertSee($products[0]->name)
            ->assertDontSee($products[5]->name)
            ->assertDontSee($products[6]->name);
    }

    public function test_dashboard_escapes_product_and_customer_names(): void
    {
        $product = Product::factory()->create(['name' => '<script>alert(1)</script>', 'stock' => 1]);
        $order = Order::factory()->create(['customer_name' => '<img src=x onerror=alert(2)>']);

        $this->actingAs($this->admin())->get('/admin')->assertOk()
            ->assertSee($product->name)->assertDontSee($product->name, false)
            ->assertSee($order->customer_name)->assertDontSee($order->customer_name, false);
    }

    public function test_login_returns_admins_to_dashboard_and_preserves_intended_pages(): void
    {
        $admin = $this->admin();

        $this->post('/admin/login', ['email' => $admin->email, 'password' => 'password'])->assertRedirect('/admin');
        $this->get('/admin/login')->assertRedirect('/admin');
        $this->post('/admin/logout')->assertRedirect('/admin/login');
        $this->get('/admin/orders')->assertRedirect('/admin/login');
        $this->post('/admin/login', ['email' => $admin->email, 'password' => 'password'])->assertRedirect('/admin/orders');

    }
}
