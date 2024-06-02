<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'username' => $this->faker->username(),
            'password' => Hash::make('123'),
            'first_name' => $this->faker->firstName(),
            'middle_name' => $this->faker->lastName(),
            'last_name' => $this->faker->lastName(),
            'email' => $this->faker->unique()->safeEmail(),
            'gender' => $this->faker->randomElement(['male', 'female']),
            'profile_pic_path' => '/ProfilePic/avatar'.$this->faker->randomElement(['1', '2','3','4']).'.PNG',
            'address' => $this->faker->address(),
            'email' => $this->faker->unique()->safeEmail(),
        ];
    }
}
