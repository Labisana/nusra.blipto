<?php

namespace Database\Seeders;

use App\Enums\TransactionActivity;
use App\Enums\TransactionDirection;
use App\Models\Customer;
use App\Models\Item;
use App\Models\StockTransaction;
use App\Models\User;
use App\Services\Inventory\StockTransactionService;
use Illuminate\Database\Seeder;

class StockTransactionSeeder extends Seeder
{
    public function run(StockTransactionService $service): void
    {
        if (StockTransaction::query()->exists()) {
            return;
        }

        /** @var User $admin */
        $admin = User::query()->where('email', 'admin@nusra.test')->firstOrFail();

        $items = Item::query()->pluck('id', 'sku');
        $customers = Customer::query()->pluck('id', 'cid');

        $incomingDate = now()->subDays(3)->toDateString();
        $outgoingDate = now()->subDays(2)->toDateString();
        $dismantleDate = now()->subDays(1)->toDateString();

        // 1. Penerimaan FPB (tanpa pelanggan)
        $service->create(TransactionDirection::In, [
            'activity' => TransactionActivity::Fpb->value,
            'customer_id' => null,
            'transaction_date' => $incomingDate,
            'reference' => 'FPB-'.now()->format('Ymd').'-001',
            'note' => 'Penerimaan stok gudang dari FPB.',
            'items' => [
                [
                    'item_id' => $items['RT-MIK-RB750'],
                    'quantity' => 5,
                    'serials' => ['RB750-001', 'RB750-002', 'RB750-003', 'RB750-004', 'RB750-005'],
                ],
                [
                    'item_id' => $items['ONT-ZTE-F609'],
                    'quantity' => 12,
                    'serials' => array_map(fn (int $i): string => 'F609-A'.str_pad((string) $i, 2, '0', STR_PAD_LEFT), range(1, 12)),
                ],
                ['item_id' => $items['CBL-UTP-C6'], 'quantity' => 100, 'serials' => []],
                ['item_id' => $items['ACC-RJ45'], 'quantity' => 100, 'serials' => []],
                ['item_id' => $items['PATCH-3M'], 'quantity' => 40, 'serials' => []],
                ['item_id' => $items['SFP-1G'], 'quantity' => 10, 'serials' => []],
            ],
        ], $admin);

        // 2. Instalasi ke pelanggan (barang serial)
        $service->create(TransactionDirection::Out, [
            'activity' => TransactionActivity::Instalasi->value,
            'customer_id' => $customers['C-001'],
            'transaction_date' => $outgoingDate,
            'reference' => 'DO-'.now()->format('Ymd').'-001',
            'note' => 'Instalasi router baru.',
            'items' => [
                [
                    'item_id' => $items['RT-MIK-RB750'],
                    'quantity' => 2,
                    'serials' => ['RB750-001', 'RB750-002'],
                ],
            ],
        ], $admin);

        // 3. Maintenance (barang non-serial)
        $service->create(TransactionDirection::Out, [
            'activity' => TransactionActivity::Maintenance->value,
            'customer_id' => $customers['C-003'],
            'transaction_date' => $outgoingDate,
            'reference' => 'DO-'.now()->format('Ymd').'-002',
            'note' => 'Perbaikan jaringan kantor.',
            'items' => [
                ['item_id' => $items['PATCH-3M'], 'quantity' => 10, 'serials' => []],
                ['item_id' => $items['ACC-RJ45'], 'quantity' => 20, 'serials' => []],
            ],
        ], $admin);

        // 4. Peminjaman perangkat
        $service->create(TransactionDirection::Out, [
            'activity' => TransactionActivity::Peminjaman->value,
            'customer_id' => $customers['C-002'],
            'transaction_date' => $outgoingDate,
            'reference' => 'DO-'.now()->format('Ymd').'-003',
            'note' => 'Peminjaman ONU untuk pengujian.',
            'items' => [
                [
                    'item_id' => $items['ONT-ZTE-F609'],
                    'quantity' => 3,
                    'serials' => ['F609-A01', 'F609-A02', 'F609-A03'],
                ],
            ],
        ], $admin);

        // 5. Dismantle: perangkat kembali dari pelanggan
        $service->create(TransactionDirection::In, [
            'activity' => TransactionActivity::Dismantle->value,
            'customer_id' => $customers['C-002'],
            'transaction_date' => $dismantleDate,
            'reference' => 'RET-'.now()->format('Ymd').'-001',
            'note' => 'Dismantle 2 unit ONU.',
            'items' => [
                [
                    'item_id' => $items['ONT-ZTE-F609'],
                    'quantity' => 2,
                    'serials' => ['F609-A01', 'F609-A02'],
                ],
            ],
        ], $admin);
    }
}
