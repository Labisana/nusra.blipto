<?php

namespace Database\Factories;

use App\Models\Item;
use App\Models\StockTransaction;
use App\Models\StockTransactionItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<StockTransactionItem>
 */
class StockTransactionItemFactory extends Factory
{
    protected $model = StockTransactionItem::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'stock_transaction_id' => StockTransaction::factory(),
            'item_id' => Item::factory(),
            'quantity' => fake()->numberBetween(1, 10),
        ];
    }
}
