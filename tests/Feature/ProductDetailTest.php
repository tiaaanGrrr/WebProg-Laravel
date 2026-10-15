<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class ProductDetailTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_detail_displays_product_and_quantity_controls(): void
    {
        $product = Product::factory()->create(['price' => 125000, 'stock' => 7]);

        $this->get('/products/'.$product->id)->assertOk()->assertSee($product->name)->assertSee($product->description)
            ->assertSee('Rp 125.000')->assertSee('Stok tersedia: 7')->assertSee('data-product-quantity', false)
            ->assertSee('max="7"', false)->assertSee('data-product-id="database-'.$product->id.'"', false);
    }

    public function test_quantity_controls_cap_at_cart_limit(): void
    {
        $product = Product::factory()->create(['stock' => 150]);

        $this->get('/products/'.$product->id)->assertOk()->assertSee('max="99"', false);
    }

    public function test_out_of_stock_detail_is_visible_without_purchase_controls(): void
    {
        $product = Product::factory()->create(['stock' => 0]);

        $this->get('/products/'.$product->id)->assertOk()->assertSee('STOK HABIS')->assertDontSee('data-product-quantity', false)->assertDontSee('data-add-to-cart', false);
    }

    public function test_missing_and_non_numeric_products_return_not_found(): void
    {
        $this->get('/products/99999')->assertNotFound();
        $this->get('/products/not-a-product')->assertNotFound();
    }

    public function test_related_products_are_available_same_category_and_limited_to_four(): void
    {
        $product = Product::factory()->create(['category' => 'Hio']);
        $related = Product::factory()->count(5)->create(['category' => 'Hio']);
        $other = Product::factory()->create(['category' => 'Lilin']);
        $soldOut = Product::factory()->create(['category' => 'Hio', 'stock' => 0]);

        $this->get('/products/'.$product->id)->assertOk()
            ->assertViewHas('relatedProducts', fn ($products): bool => $products->modelKeys() === [$related[4]->id, $related[3]->id, $related[2]->id, $related[1]->id])
            ->assertDontSee($other->name)->assertDontSee($soldOut->name)->assertDontSee($related[0]->name);
    }

    public function test_product_and_bundle_cards_link_to_details(): void
    {
        $product = Product::factory()->create();
        $bundle = Product::factory()->create(['category' => 'Paket']);

        $this->get('/')->assertOk()->assertSee(route('products.show', $product))->assertSee(route('products.show', $bundle));
        $this->get('/products')->assertOk()->assertSee(route('products.show', $product));
    }

    public function test_detail_escapes_product_content(): void
    {
        $product = Product::factory()->create(['name' => '<script>alert(1)</script>', 'description' => '<img src=x onerror=alert(1)>']);

        $this->get('/products/'.$product->id)->assertOk()->assertSee($product->name)->assertDontSee($product->name, false)
            ->assertSee($product->description)->assertDontSee($product->description, false);
    }
}
