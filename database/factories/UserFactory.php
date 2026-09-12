<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'middle_initial' => fake()->randomLetter(),
            'sex' => fake()->randomElement(['male', 'female', 'other']),
            'email' => fake()->unique()->safeEmail(),
            'contact_no' => fake()->numerify('09#########'),
            'birthday' => fake()->dateTimeBetween('-40 years', '-18 years')->format('Y-m-d'),
            'age' => 25,
            'province' => fake()->city(),
            'municipality' => fake()->city(),
            'barangay' => fake()->streetName(),
            'street' => fake()->streetAddress(),
            'house_number' => fake()->buildingNumber(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
