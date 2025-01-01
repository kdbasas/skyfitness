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
    protected $month = 1;

    public function definition(): array
    {
        $subscriptionName = $this->generateSubscriptionName();
        $this->month++;

        return [
            'subscription_name' => $subscriptionName,
            'validity' => $this->month - 1,
            'amount' => 800 + (($this->month - 1) * 800),
        ];
    }

    protected function generateSubscriptionName()
    {
        if ($this->month > 12) {
            $this->month = 1;
        }
        return $this->month . ' months';
    }
}