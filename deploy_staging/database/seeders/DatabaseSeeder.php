<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            BtsBscSeeder::class,
            CustomerSeeder::class,
            ItemSeeder::class,
            StockTransactionSeeder::class,
        ]);
    }
}
