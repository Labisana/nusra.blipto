<?php

namespace Database\Factories;

use App\Enums\SerialStatus;
use App\Models\Item;
use App\Models\SerialNumber;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SerialNumber>
 */
class SerialNumberFactory extends Factory
{
    protected $model = SerialNumber::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'item_id' => Item::factory()->serial(),
            'serial_number' => strtoupper(fake()->unique()->bothify('SN-####-????')),
            'status' => SerialStatus::Available,
            'customer_id' => null,
            'stock_transaction_item_id' => null,
            'received_at' => now()->toDateString(),
            'issued_at' => null,
        ];
    }

    public function issued(): static
    {
        return $this->state(fn (): array => [
            'status' => SerialStatus::Issued,
            'issued_at' => now()->toDateString(),
        ]);
    }
}
