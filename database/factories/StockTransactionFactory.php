<?php

namespace Database\Factories;

use App\Enums\TransactionActivity;
use App\Enums\TransactionDirection;
use App\Models\StockTransaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<StockTransaction>
 */
class StockTransactionFactory extends Factory
{
    protected $model = StockTransaction::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'reference' => strtoupper(fake()->unique()->bothify('TRX-####-????')),
            'direction' => TransactionDirection::In,
            'activity' => TransactionActivity::Fpb,
            'customer_id' => null,
            'transaction_date' => now()->toDateString(),
            'note' => null,
            'user_id' => User::factory(),
        ];
    }

    public function outgoing(): static
    {
        return $this->state(fn (): array => [
            'direction' => TransactionDirection::Out,
            'activity' => TransactionActivity::Instalasi,
        ]);
    }
}
