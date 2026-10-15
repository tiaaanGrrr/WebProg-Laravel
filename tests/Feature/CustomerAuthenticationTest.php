<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CustomerAuthenticationTest extends TestCase
{
    use LazilyRefreshDatabase;

    private function registration(): array
    {
        return ['name' => 'Pembeli Baru', 'email' => 'pembeli@example.test', 'password' => 'Password123!', 'password_confirmation' => 'Password123!'];
    }

    public function test_registration_logs_in_customer_hashes_password_and_rejects_privilege_injection(): void
    {
        $this->get('/register')->assertOk()->assertSee('Konfirmasi password');
        $this->post('/register', [...$this->registration(), 'email' => 'PEMBELI@example.test', 'is_admin' => true])->assertRedirect('/orders');

        $user = User::sole();
        $this->assertAuthenticatedAs($user, 'customer');
        $this->assertGuest('web');
        $this->assertFalse($user->is_admin);
        $this->assertTrue(Hash::check('Password123!', $user->password));
        $this->assertSame('pembeli@example.test', $user->email);
        $this->get('/login')->assertRedirect('/orders');
        $this->get('/admin')->assertRedirect('/admin/login');
    }

    public function test_invalid_and_duplicate_registration_is_rejected(): void
    {
        User::factory()->create(['email' => 'pembeli@example.test']);

        $this->post('/register', [])->assertSessionHasErrors(['name', 'email', 'password']);
        $this->post('/register', [...$this->registration(), 'password_confirmation' => 'Different'])->assertSessionHasErrors(['password', 'email']);
        $this->post('/register', [...$this->registration(), 'email' => ['invalid']])->assertSessionHasErrors('email');
        $this->assertDatabaseCount('users', 1);
        $this->assertGuest('customer');
    }

    public function test_admin_credentials_and_wrong_password_cannot_login_as_customer(): void
    {
        $admin = User::factory()->create();
        $admin->is_admin = true;
        $admin->save();
        $buyer = User::factory()->create();

        $this->post('/login', ['email' => $admin->email, 'password' => 'password'])->assertSessionHasErrors(['email' => 'Email atau password pembeli salah.']);
        $this->post('/login', ['email' => $buyer->email, 'password' => 'wrong'])->assertSessionHasErrors('email');
        $this->assertGuest('customer');
        $this->post('/admin/login', ['email' => $buyer->email, 'password' => 'password'])->assertSessionHasErrors('email');
        $this->assertGuest('web');
    }

    public function test_guest_cart_survives_registration_and_order_is_bound_to_logged_in_customer(): void
    {
        $product = Product::factory()->create();
        $other = User::factory()->create();
        $this->post('/checkout/preview', ['items' => [['product_id' => $product->id, 'quantity' => 2]]])->assertRedirect('/checkout');
        $this->get('/checkout')->assertRedirect('/login');
        $this->post('/register', $this->registration())->assertRedirect('/checkout');
        $this->get('/checkout')->assertOk()->assertSee('Pembeli Baru');
        $token = session('checkout.token');
        $buyer = User::where('email', 'pembeli@example.test')->firstOrFail();

        $this->post('/checkout', ['checkout_token' => $token, 'customer_name' => 'Pembeli Baru', 'phone' => '081234567890', 'address' => 'Jalan Pancoran nomor 42', 'user_id' => $other->id])->assertRedirect();

        $order = Order::sole();
        $this->assertSame($buyer->id, $order->user_id);
        $this->post('/logout')->assertRedirect('/login');
        $this->assertGuest('customer');
        $this->post('/login', ['email' => $buyer->email, 'password' => 'Password123!'])->assertRedirect('/orders');
        $this->get('/orders')->assertOk()->assertSee($order->reference);
        $this->get('/orders/'.$order->reference)->assertOk();
    }

    public function test_legacy_session_orders_are_claimed_without_taking_other_accounts_orders(): void
    {
        $buyer = User::factory()->create();
        $other = User::factory()->create();
        $legacy = Order::factory()->create();
        $ownedByOther = Order::factory()->for($other)->create();

        $this->withSession(['order_references' => [$legacy->reference, $ownedByOther->reference]])->post('/login', ['email' => $buyer->email, 'password' => 'password'])->assertRedirect('/orders');

        $this->assertDatabaseHas('orders', ['id' => $legacy->id, 'user_id' => $buyer->id]);
        $this->assertDatabaseHas('orders', ['id' => $ownedByOther->id, 'user_id' => $other->id]);
        $this->get('/orders/'.$ownedByOther->reference)->assertNotFound();
        $this->get(route('checkout.success', $ownedByOther->reference))->assertNotFound();
        $this->get('/orders')->assertOk()->assertSee($legacy->reference)->assertDontSee($ownedByOther->reference);
    }

    public function test_customer_and_admin_sessions_remain_independent_on_logout(): void
    {
        $admin = User::factory()->create();
        $admin->is_admin = true;
        $admin->save();
        $this->post('/admin/login', ['email' => $admin->email, 'password' => 'password'])->assertRedirect('/admin');
        $this->post('/register', $this->registration())->assertRedirect('/orders');
        $buyer = User::where('email', 'pembeli@example.test')->firstOrFail();

        $this->post('/logout')->assertRedirect('/login');
        $this->assertAuthenticatedAs($admin, 'web');
        $this->assertGuest('customer');
        $this->post('/login', ['email' => $buyer->email, 'password' => 'Password123!'])->assertRedirect('/orders');
        $this->post('/admin/logout')->assertRedirect('/admin/login');
        $this->assertGuest('web');
        $this->assertAuthenticatedAs($buyer, 'customer');
        $this->get('/orders')->assertOk();
    }

    public function test_guest_cannot_submit_checkout_and_customer_cannot_view_another_accounts_order(): void
    {
        $this->post('/checkout', [])->assertRedirect('/login');
        $buyer = User::factory()->create();
        $other = User::factory()->create();
        $order = Order::factory()->for($other)->create();

        $this->actingAs($buyer, 'customer')->withSession(['order_references' => [$order->reference]])->get('/orders/'.$order->reference)->assertNotFound();
        $this->get(route('checkout.success', $order->reference))->assertNotFound();
        $this->get('/orders')->assertOk()->assertDontSee($order->reference);
    }
}
