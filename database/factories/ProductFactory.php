<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    public function definition(): array
    {
        // Set the current date and time in the Asia/Manila timezone
        $dateTime = now()->setTimezone('Asia/Manila');
        return [
            'name' => $this->faker->word,
            'category' => $this->faker->randomElement(['best seller', 'budget product', 'new arrival']),
            'description' => $this->faker->sentence,
            'date_and_time' => $dateTime->format('Y-m-d H:i:s'),
        ];
    }
}
