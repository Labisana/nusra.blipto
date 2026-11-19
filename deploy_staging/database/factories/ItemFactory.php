<?php

namespace Database\Factories;

use App\Enums\ItemType;
use App\Models\Item;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Item>
 */
class ItemFactory extends Factory
{
    protected $model = Item::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'sku' => strtoupper(fake()->unique()->bothify('???-####')),
            'name' => fake()->words(3, true),
            'type' => ItemType::NonSerial,
            'unit' => 'pcs',
            'location' => 'Rak '.fake()->randomLetter().fake()->numberBetween(1, 5),
            'min_stock' => fake()->numberBetween(0, 10),
        ];
    }

    public function serial(): static
    {
        return $this->state(fn (): array => ['type' => ItemType::Serial]);
    }
}
