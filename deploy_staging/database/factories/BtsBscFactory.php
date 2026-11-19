<?php

namespace Database\Factories;

use App\Enums\BtsStatus;
use App\Models\BtsBsc;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BtsBsc>
 */
class BtsBscFactory extends Factory
{
    protected $model = BtsBsc::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama' => 'BTS-'.fake()->unique()->bothify('???-##'),
            'alamat' => fake()->address(),
            'status' => BtsStatus::Aktif,
        ];
    }

    public function nonAktif(): static
    {
        return $this->state(fn (): array => ['status' => BtsStatus::NonAktif]);
    }
}
