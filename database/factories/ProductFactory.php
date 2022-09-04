<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'product_name' => 'Name of Product'.rand(10,200),
            'product_price' => mt_rand(1000, 5000),
            'image' => 'IMG-100'.rand(10, 500),
            'status'=>1,
        ];
    }
}
