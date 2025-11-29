<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'customer_id' => \App\Models\Customer::factory(),
            'total_amount' => $this->faker->randomFloat(2, 50, 1000),
            'paid_amount' => $this->faker->randomFloat(2, 1000, 2000),
            'change_amount' => 0, // Will be calculated in seeder or ignored
            'payment_method' => $this->faker->randomElement(['cash', 'credit_card', 'debit_card']),
            'created_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
        ];
    }
}
