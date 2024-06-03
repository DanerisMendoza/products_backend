<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\ProductImages;
use Faker\Factory as Faker;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Product::factory(10)->create();
        $dateTime = now()->setTimezone('Asia/Manila');
        $faker = Faker::create();
        for ($i = 0; $i < 5; $i++) {
            // Create a user
            $Product = Product::create([
                'name' => $faker->word,
                'category' => $faker->randomElement(['best seller', 'budget product', 'new arrival']),
                'description' => $faker->sentence,
                'date_and_time' => $dateTime->format('Y-m-d H:i:s'),
            ]);

            // Create a user detail for the user
            ProductImages::create([
                'product_id' => $Product->id,
                'path' => '/product_pictures/sample'. $faker->randomElement(['1', '2', '3']) . '.png',
            ]);
            ProductImages::create([
                'product_id' => $Product->id,
                'path' => '/product_pictures/sample'. $faker->randomElement(['1', '2', '3']) . '.png',
            ]);
            ProductImages::create([
                'product_id' => $Product->id,
                'path' => '/product_pictures/sample'. $faker->randomElement(['1', '2', '3']) . '.png',
            ]);
        }
    }
}
