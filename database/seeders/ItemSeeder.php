<?php

namespace Database\Seeders;

use App\Enums\ItemType;
use App\Models\Item;
use Illuminate\Database\Seeder;

class ItemSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            ['sku' => 'RT-MIK-RB750', 'name' => 'Router Mikrotik RB750', 'type' => ItemType::Serial, 'unit' => 'unit', 'location' => 'Rak A1', 'min_stock' => 5],
            ['sku' => 'ONT-ZTE-F609', 'name' => 'ONU/ONT ZTE F609', 'type' => ItemType::Serial, 'unit' => 'unit', 'location' => 'Rak A2', 'min_stock' => 5],
            ['sku' => 'CBL-UTP-C6', 'name' => 'Kabel UTP Cat6', 'type' => ItemType::NonSerial, 'unit' => 'box', 'location' => 'Rak B1', 'min_stock' => 10],
            ['sku' => 'ACC-RJ45', 'name' => 'Konektor RJ45', 'type' => ItemType::NonSerial, 'unit' => 'pack', 'location' => 'Rak B2', 'min_stock' => 20],
            ['sku' => 'SFP-1G', 'name' => 'SFP Module 1G', 'type' => ItemType::NonSerial, 'unit' => 'pcs', 'location' => 'Rak C1', 'min_stock' => 5],
            ['sku' => 'PATCH-3M', 'name' => 'Patch Cord 3m', 'type' => ItemType::NonSerial, 'unit' => 'pcs', 'location' => 'Rak C2', 'min_stock' => 10],
        ];

        foreach ($items as $item) {
            Item::query()->updateOrCreate(['sku' => $item['sku']], $item);
        }
    }
}
