<?php

namespace Database\Factories\Admin;

use App\Models\Admin\Registration;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Registration>
 */
class RegistrationFactory extends Factory
{
    protected $model = Registration::class;

    public function definition(): array
    {
        return [
            'user_type' => fake()->randomElement(['buyer', 'seller', 'logistics', 'rider']),
            'last_name' => fake()->lastName(),
            'first_name' => fake()->firstName(),
            'middle_name' => fake()->optional()->firstName(),
            'sex' => fake()->randomElement(['male', 'female', 'other']),
            'birthdate' => fake()->dateTimeBetween('-50 years', '-18 years')->format('Y-m-d'),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->numerify('09#########'),
            'password' => bcrypt('password'),
            'province' => fake()->city(),
            'municipality' => fake()->city(),
            'barangay' => fake()->streetName(),
            'street' => fake()->streetAddress(),
            'house_no' => fake()->buildingNumber(),
            'zip_code' => fake()->numerify('####'),
            'business_name' => fake()->optional()->company(),
            'business_category' => fake()->optional()->word(),
            'business_permit_path' => null,
            'valid_id_path' => 'registration-documents/' . fake()->uuid() . '.jpg',
            'status' => 'pending',
        ];
    }
}
