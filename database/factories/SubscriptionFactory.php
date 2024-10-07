<?php

namespace Database\Factories;


use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Subscription;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Subscription>
 */
class SubscriptionFactory extends Factory
{
    protected $model = Subscription::class;

    public function definition(): array
    {
        return [
            'subscription_name' => $this->faker->word(),
            'validity' => $this->faker->numberBetween(1, 12), // Duration in months
            'amount' => $this->faker->randomFloat(2, 10, 100), // Random amount between 10 and 100
        ];
    }
}
