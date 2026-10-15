<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;

class AdminListFilterTest extends TestCase
{
    use LazilyRefreshDatabase;

    private function admin(): User
    {
        $user = User::factory()->create();
        $user->is_admin = true;
        $user->save();

        return $user;
    }

    public function test_product_search_combines_keyword_category_and_stock(): void
    {
        $target = Product::factory()->create(['name' => 'Produk Pilihan', 'description' => 'Hio berkualitas', 'category' => 'Hio', 'stock' => 3]);
        $wrongCategory = Product::factory()->create(['name' => 'Hio Kategori Lain', 'category' => 'Lilin', 'stock' => 3]);
        $wrongStock = Product::factory()->create(['name' => 'Hio Stok Banyak', 'category' => 'Hio', 'stock' => 9]);

        $this->actingAs($this->admin())->get('/admin/products?'.http_build_query(['q' => 'Hio', 'category' => 'Hio', 'stock' => 'menipis']))->assertOk()
            ->assertSee($target->name)->assertDontSee($wrongCategory->name)->assertDontSee($wrongStock->name)
            ->assertViewHas('products', fn ($products): bool => $products->total() === 1);
    }

    #[TestWith(['tersedia', [6, 5, 1]])]
    #[TestWith(['menipis', [5, 1]])]
    #[TestWith(['habis', [0]])]
    public function test_stock_filters_respect_boundaries(string $filter, array $expected): void
    {
        Product::factory()->count(4)->sequence(['stock' => 0], ['stock' => 1], ['stock' => 5], ['stock' => 6])->create();

        $this->actingAs($this->admin())->get('/admin/products?stock='.$filter)->assertOk()
            ->assertViewHas('products', fn ($products): bool => $products->pluck('stock')->all() === $expected);
    }

    #[TestWith(['CS-TARGET'])]
    #[TestWith(['BudiTarget'])]
    #[TestWith(['081999'])]
    public function test_order_search_matches_reference_name_or_phone_and_keeps_status_filter(string $keyword): void
    {
        $target = Order::factory()->create(['reference' => 'CS-TARGET', 'customer_name' => 'BudiTarget', 'phone' => '081999123456', 'status' => 'diproses']);
        $other = Order::factory()->create(['reference' => 'CS-TARGET-OTHER', 'customer_name' => 'BudiTarget Lain', 'phone' => '081999123457', 'status' => 'dibatalkan']);

        $this->actingAs($this->admin())->get('/admin/orders?'.http_build_query(['q' => $keyword, 'status' => 'diproses']))->assertOk()
            ->assertSee($target->reference)->assertDontSee($other->reference)
            ->assertViewHas('orders', fn ($orders): bool => $orders->total() === 1);
    }

    public function test_product_pagination_retains_all_filters(): void
    {
        $products = Product::factory()->count(12)->create(['name' => 'Hio Pilihan', 'category' => 'Hio', 'stock' => 2]);
        $url = '/admin/products?q=Hio&category=Hio&stock=menipis';

        $this->actingAs($this->admin())->get($url)->assertOk()->assertSee('Selanjutnya')
            ->assertSee('q=Hio', false)->assertSee('category=Hio', false)->assertSee('stock=menipis', false)->assertSee('page=2', false)
            ->assertViewHas('products', fn ($listed): bool => $listed->count() === 10 && $listed->total() === 12);
        $this->get($url.'&page=2')->assertOk()
            ->assertViewHas('products', fn ($listed): bool => $listed->getCollection()->modelKeys() === [$products[1]->id, $products[0]->id]);
    }

    public function test_order_pagination_retains_status_and_keyword(): void
    {
        $orders = Order::factory()->count(11)->create(['customer_name' => 'Penerima Khusus', 'status' => 'baru']);
        Order::factory()->create(['customer_name' => 'Penerima Khusus', 'status' => 'selesai']);
        $url = '/admin/orders?q=Khusus&status=baru';

        $this->actingAs($this->admin())->get($url)->assertOk()->assertSee('q=Khusus', false)->assertSee('status=baru', false)->assertSee('page=2', false)
            ->assertViewHas('orders', fn ($listed): bool => $listed->count() === 10 && $listed->total() === 11);
        $this->get($url.'&page=2')->assertOk()
            ->assertViewHas('orders', fn ($listed): bool => $listed->getCollection()->modelKeys() === [$orders[0]->id]);
    }

    public function test_empty_results_and_zero_keyword_are_handled(): void
    {
        $product = Product::factory()->create(['name' => 'Lilin 0', 'description' => 'Lilin putih']);
        Product::factory()->create(['name' => 'Dupa', 'description' => 'Dupa wangi']);

        $this->actingAs($this->admin())->get('/admin/products?q=0')->assertOk()->assertSee($product->name)
            ->assertViewHas('products', fn ($products): bool => $products->total() === 1);
        $this->get('/admin/products?q=TidakAda')->assertOk()->assertSee('Tidak ada produk pada daftar ini.');
        $this->get('/admin/orders?q=TidakAda')->assertOk()->assertSee('Tidak ada pesanan pada daftar ini.');
    }

    public function test_invalid_filters_are_rejected(): void
    {
        $this->actingAs($this->admin())->getJson('/admin/products?stock=invalid&page=0&q[]=x')->assertUnprocessable()->assertJsonValidationErrors(['stock', 'page', 'q']);
        $this->getJson('/admin/orders?status=invalid&page=-1')->assertUnprocessable()->assertJsonValidationErrors(['status', 'page']);
    }

    public function test_search_is_bound_and_user_input_is_escaped(): void
    {
        Product::factory()->create();
        Order::factory()->create();
        $keyword = "' OR 1=1 --";

        $this->actingAs($this->admin())->get('/admin/products?'.http_build_query(['q' => $keyword]))->assertOk()
            ->assertViewHas('products', fn ($products): bool => $products->total() === 0);
        $this->get('/admin/orders?'.http_build_query(['q' => $keyword]))->assertOk()
            ->assertViewHas('orders', fn ($orders): bool => $orders->total() === 0);
        $this->get('/admin/products?'.http_build_query(['q' => '<script>alert(1)</script>']))->assertOk()->assertSee('<script>alert(1)</script>')->assertDontSee('<script>alert(1)</script>', false);
    }
}
