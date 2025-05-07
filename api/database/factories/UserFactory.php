<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),

/*
 * us only since we are using us weather service for now, can set different api and use differently
Longitude: -125.0° W to -66.9° W
Latitude: 24.5° N to 49.0° N
*/
            'longitude' => fake()->longitude(-125, -66.9),
            'latitude' => fake()->latitude(24.5,49.0),
        ];
    }
}
