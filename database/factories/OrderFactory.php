<?php

namespace Database\Factories;

use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'reference' => 'CS-'.strtoupper(fake()->unique()->bothify('########??')),
            'checkout_token' => fake()->uuid(),
            'customer_name' => fake()->name(),
            'phone' => '081234567890',
            'address' => fake()->address(),
            'notes' => null,
            'total' => 125000,
            'status' => 'baru',
        ];
    }
}
