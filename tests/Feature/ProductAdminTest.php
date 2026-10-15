<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductAdminTest extends TestCase
{
    use LazilyRefreshDatabase;

    private function admin(): User
    {
        $user = User::factory()->create();
        $user->is_admin = true;
        $user->save();

        return $user;
    }

    public function test_guest_and_regular_user_cannot_manage_products(): void
    {
        $this->get('/admin/products')->assertRedirect('/admin/login');
        $this->actingAs(User::factory()->create())->get('/admin/products')->assertForbidden();
        $this->post('/admin/products', [])->assertForbidden();
        $this->assertDatabaseCount('products', 0);
    }

    public function test_admin_can_login_and_logout(): void
    {
        $admin = $this->admin();
        $this->post('/admin/login', ['email' => $admin->email, 'password' => 'password'])->assertRedirect('/admin');
        $this->assertAuthenticatedAs($admin);
        $this->post('/admin/logout')->assertRedirect('/admin/login');
        $this->assertGuest();
    }

    public function test_non_admin_and_incorrect_password_are_rejected(): void
    {
        $user = User::factory()->create();
        $this->post('/admin/login', ['email' => $user->email, 'password' => 'password'])->assertSessionHasErrors(['email' => 'Email atau password admin salah.']);
        $this->assertGuest();
        $admin = $this->admin();
        $this->post('/admin/login', ['email' => $admin->email, 'password' => 'wrong'])->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_admin_pages_render(): void
    {
        $product = Product::factory()->create();
        $this->get('/admin/login')->assertOk()->assertSee('Login Admin');
        $this->actingAs($this->admin())->get('/admin/products')->assertOk()->assertSee($product->name);
        $this->get('/admin/products/create')->assertOk()->assertSee('Tambah Produk');
        $this->get('/admin/products/'.$product->id.'/edit')->assertOk()->assertSee($product->name);
    }

    public function test_admin_can_create_product_with_locally_served_image(): void
    {
        Storage::fake('local');
        $data = Product::factory()->make()->only(['name', 'category', 'description', 'price', 'stock']);
        $data['image'] = new UploadedFile(public_path('images/cahaya/product-hio.jpg'), 'hio.jpg', 'image/jpeg', null, true);
        $this->actingAs($this->admin())->post('/admin/products', $data)->assertRedirect('/admin/products');
        $product = Product::sole();
        $this->assertDatabaseHas('products', ['name' => $data['name'], 'price' => $data['price']]);
        Storage::disk('local')->assertExists(substr($product->image, 8));
        $this->get('/'.$product->image)->assertOk()->assertHeader('X-Content-Type-Options', 'nosniff');
        $this->get('/')->assertOk()->assertSee($data['name'])->assertSee('database-'.$product->id);
        $this->get('/products')->assertOk()->assertSee($data['name'])->assertSee('database-'.$product->id);
    }

    public function test_update_keeps_image_then_replaces_and_deletes_upload(): void
    {
        Storage::fake('local');
        $product = Product::factory()->create();
        $data = $product->only(['name', 'category', 'description', 'price', 'stock']);
        $data['price'] = 99000;
        $this->actingAs($this->admin())->put('/admin/products/'.$product->id, $data)->assertRedirect('/admin/products');
        $this->assertDatabaseHas('products', ['id' => $product->id, 'price' => 99000, 'image' => 'images/cahaya/product-hio.jpg']);
        $data['image'] = new UploadedFile(public_path('images/cahaya/product-hio.jpg'), 'new.jpg', 'image/jpeg', null, true);
        $this->put('/admin/products/'.$product->id, $data)->assertRedirect('/admin/products');
        $product->refresh();
        $oldUpload = substr($product->image, 8);
        Storage::disk('local')->assertExists($oldUpload);
        $data['image'] = new UploadedFile(public_path('images/cahaya/product-lilin.jpg'), 'replacement.jpg', 'image/jpeg', null, true);
        $this->put('/admin/products/'.$product->id, $data)->assertRedirect('/admin/products');
        Storage::disk('local')->assertMissing($oldUpload);
        $product->refresh();
        $upload = substr($product->image, 8);
        $this->delete('/admin/products/'.$product->id)->assertRedirect('/admin/products');
        $this->assertModelMissing($product);
        Storage::disk('local')->assertMissing($upload);
    }

    public function test_invalid_product_is_not_saved(): void
    {
        Storage::fake('local');
        $this->actingAs($this->admin())->post('/admin/products', [])->assertSessionHasErrors(['name', 'category', 'description', 'price', 'stock', 'image']);
        $data = Product::factory()->make()->only(['name', 'category', 'description', 'price', 'stock']);
        $data['stock'] = -1;
        $data['price'] = 0;
        $data['image'] = UploadedFile::fake()->create('bad.txt', 1, 'text/plain');
        $this->post('/admin/products', $data)->assertSessionHasErrors(['stock' => 'Stok tidak boleh negatif.', 'price' => 'Harga minimal Rp 1.', 'image']);
        $this->assertDatabaseCount('products', 0);
        Storage::disk('local')->assertDirectoryEmpty('products');
    }

    public function test_out_of_stock_products_are_hidden_and_content_is_escaped(): void
    {
        $hidden = Product::factory()->create(['stock' => 0]);
        $product = Product::factory()->create(['name' => '<script>alert(1)</script>', 'description' => '<img src=x onerror=alert(1)>']);
        $this->get('/')->assertOk()->assertDontSee($hidden->name)->assertSee($product->name)->assertDontSee($product->name, false);
        $this->get('/products')->assertOk()->assertDontSee($hidden->name)->assertSee($product->description)->assertDontSee($product->description, false);
        $this->actingAs($this->admin())->get('/admin/products')->assertOk()->assertSee($hidden->name);
    }

    public function test_create_admin_command_creates_an_account_that_can_login(): void
    {
        $this->artisan('app:create-admin', ['email' => 'admin@example.test'])
            ->expectsQuestion('Password admin (minimal 8 karakter)', 'KuliahPassword123')
            ->expectsOutput('Akun admin berhasil dibuat. Login melalui /admin/login.')
            ->assertExitCode(0);

        $this->assertDatabaseHas('users', ['email' => 'admin@example.test', 'is_admin' => true]);
        $this->post('/admin/login', ['email' => 'admin@example.test', 'password' => 'KuliahPassword123'])->assertRedirect('/admin');
        $this->assertAuthenticated();
    }
}
