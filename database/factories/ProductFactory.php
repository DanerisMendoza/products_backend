<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->word,
            'category' => $this->faker->randomElement(['best seller', 'budget product', 'new arrival']),
            'description' => $this->faker->lastName(),
        ];
    }
}
