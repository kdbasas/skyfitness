<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Member;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Member>
 */
class MemberFactory extends Factory
{
    protected $model = \App\Models\Member::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'first_name' => $this->faker->firstName(),
            'middle_name' => $this->faker->optional()->lastName(),
            'last_name' => $this->faker->lastName(),
            'suffix_name' => $this->faker->optional()->suffix(),
            'email' => $this->faker->unique()->safeEmail(),
            'contact_number' => $this->faker->phoneNumber(),
            'subscription_id' => $this->faker->numberBetween(1, 10), // Adjust range based on existing subscription IDs
            'amount' => $this->faker->randomFloat(2, 20, 500), // Adjust range as needed
            'date_joined' => $this->faker->date(),
            'date_expired' => $this->faker->optional()->date(),
            'qr_code' => $this->faker->uuid(), // Assuming UUID for QR code
        ];
    }
}