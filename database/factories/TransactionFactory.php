<?php

namespace Database\Factories;

use App\Models\Seller;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {

        $vendedor = Seller::whereHas('products')->get()->random();
        die();
        $comprador = User::all()->except($vendedor->id)->random();

        return [
            'quantity' => fake()->numberBetween(1, 10),
            'buyer_id' => $comprador->id,
            'product_id' => $vendedor->products->random()->id
        ];
    }
}
