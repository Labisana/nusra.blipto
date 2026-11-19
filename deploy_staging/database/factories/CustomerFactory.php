<?php

namespace Database\Factories;

use App\Enums\CustomerStatus;
use App\Enums\CustomerType;
use App\Models\BtsBsc;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Customer>
 */
class CustomerFactory extends Factory
{
    protected $model = Customer::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'cabang' => fake()->randomElement(['Mataram', 'Praya']),
            'item' => 'Internet Dedicated',
            'tipe_pelanggan' => fake()->randomElement(CustomerType::cases()),
            'cid' => 'C-'.fake()->unique()->numerify('####'),
            'sid' => 'S-'.fake()->unique()->numerify('####'),
            'status' => CustomerStatus::Aktif,
            'nama_pelanggan' => fake()->company(),
            'area' => fake()->city(),
            'sales' => fake()->name(),
            'alamat' => fake()->address(),
            'koordinat' => '-8.'.fake()->numerify('####').', 116.'.fake()->numerify('####'),
            'pic' => fake()->name(),
            'kontak' => fake()->phoneNumber(),
            'email' => fake()->unique()->safeEmail(),
            'lastmile' => 'Fiber Optic',
            'produk' => 'Metro-E',
            'bandwidth_main' => '100 Mbps',
            'service' => 'Managed Service',
            'segment' => 'Enterprise',
            'klasifikasi_pelanggan' => fake()->randomElement(['Platinum', 'Gold', 'Silver', 'Bronze']),
            'bts_bsc_id' => BtsBsc::factory(),
        ];
    }

    public function platinum(): static
    {
        return $this->state(fn (): array => ['klasifikasi_pelanggan' => 'Platinum']);
    }
}
