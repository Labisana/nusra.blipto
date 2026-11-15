<?php

namespace App\Services\Inventory;

use App\Enums\SerialStatus;
use App\Enums\TransactionActivity;
use App\Enums\TransactionDirection;
use App\Models\Item;
use App\Models\SerialNumber;
use App\Models\StockTransaction;
use App\Models\StockTransactionItem;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class StockTransactionService
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function create(TransactionDirection $direction, array $data, User $user): StockTransaction
    {
        return DB::transaction(function () use ($direction, $data, $user): StockTransaction {
            $activity = TransactionActivity::from($data['activity']);
            $this->assertActivityMatchesDirection($direction, $activity);

            $transaction = StockTransaction::query()->create([
                'reference' => $this->resolveReference($direction, $data),
                'direction' => $direction,
                'activity' => $activity,
                'customer_id' => $this->resolveCustomerId($activity, $data['customer_id'] ?? null),
                'transaction_date' => $data['transaction_date'],
                'note' => $data['note'] ?? null,
                'user_id' => $user->id,
            ]);

            $this->applyLines($transaction, $data['items'] ?? []);

            return $transaction->load($this->defaultRelations());
        }, 3);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(StockTransaction $transaction, array $data): StockTransaction
    {
        return DB::transaction(function () use ($transaction, $data): StockTransaction {
            $activity = TransactionActivity::from($data['activity']);
            $this->assertActivityMatchesDirection($transaction->direction, $activity);

            $this->revertLines($transaction);

            $transaction->fill([
                'reference' => $this->resolveReference($transaction->direction, $data, $transaction),
                'activity' => $activity,
                'customer_id' => $this->resolveCustomerId($activity, $data['customer_id'] ?? null),
                'transaction_date' => $data['transaction_date'],
                'note' => $data['note'] ?? null,
            ])->save();

            $this->applyLines($transaction, $data['items'] ?? []);

            return $transaction->refresh()->load($this->defaultRelations());
        }, 3);
    }

    public function delete(StockTransaction $transaction): void
    {
        DB::transaction(function () use ($transaction): void {
            $this->revertLines($transaction);
            $transaction->delete();
        }, 3);
    }

    /**
     * @return Collection<int, string>
     */
    public function availableSerials(Item $item): Collection
    {
        if (! $item->isSerial()) {
            return collect();
        }

        return $item->availableSerialNumbers()
            ->orderBy('serial_number')
            ->pluck('serial_number');
    }

    public function availableQuantity(Item $item): int
    {
        if ($item->isSerial()) {
            return $item->availableSerialNumbers()->count();
        }

        $in = (int) StockTransactionItem::query()
            ->where('item_id', $item->id)
            ->whereHas('transaction', fn ($query) => $query->where('direction', TransactionDirection::In))
            ->sum('quantity');

        $out = (int) StockTransactionItem::query()
            ->where('item_id', $item->id)
            ->whereHas('transaction', fn ($query) => $query->where('direction', TransactionDirection::Out))
            ->sum('quantity');

        return $in - $out;
    }

    /**
     * @param  array<int, array<string, mixed>>  $lines
     */
    protected function applyLines(StockTransaction $transaction, array $lines): void
    {
        foreach ($lines as $line) {
            $item = Item::query()->lockForUpdate()->findOrFail((int) $line['item_id']);
            $quantity = (int) $line['quantity'];
            $serials = $this->normalizeSerials($line['serials'] ?? []);

            $this->assertStockAvailability($transaction, $item, $quantity, $serials);

            $transactionItem = $transaction->items()->create([
                'item_id' => $item->id,
                'quantity' => $quantity,
            ]);

            if ($item->isSerial()) {
                $transaction->direction === TransactionDirection::In
                    ? $this->receiveSerials($transaction, $transactionItem, $item, $serials)
                    : $this->issueSerials($transaction, $transactionItem, $item, $serials);
            }
        }
    }

    /**
     * @param  array<int, string>  $serials
     */
    protected function assertStockAvailability(
        StockTransaction $transaction,
        Item $item,
        int $quantity,
        array $serials,
    ): void {
        if ($quantity < 1) {
            throw ValidationException::withMessages([
                'items' => "Jumlah barang {$item->name} minimal 1.",
            ]);
        }

        if ($item->isSerial()) {
            if (count($serials) !== $quantity) {
                throw ValidationException::withMessages([
                    'items' => "Jumlah serial number untuk {$item->name} harus sama dengan Qty ({$quantity}).",
                ]);
            }

            if ($transaction->direction === TransactionDirection::Out) {
                $available = SerialNumber::query()
                    ->where('item_id', $item->id)
                    ->whereIn('serial_number', $serials)
                    ->where('status', SerialStatus::Available)
                    ->count();

                if ($available !== count($serials)) {
                    throw ValidationException::withMessages([
                        'items' => "Sebagian serial number {$item->name} tidak tersedia atau sudah keluar.",
                    ]);
                }
            }

            return;
        }

        if ($transaction->direction === TransactionDirection::Out) {
            $available = $this->availableQuantity($item);

            if ($available < $quantity) {
                throw ValidationException::withMessages([
                    'items' => "Stock {$item->name} tidak mencukupi. Tersedia {$available}, diminta {$quantity}.",
                ]);
            }
        }
    }

    /**
     * @param  array<int, string>  $serials
     */
    protected function receiveSerials(
        StockTransaction $transaction,
        StockTransactionItem $transactionItem,
        Item $item,
        array $serials,
    ): void {
        foreach ($serials as $serialNumber) {
            $serial = SerialNumber::query()->firstOrNew(['serial_number' => $serialNumber]);

            if ($serial->exists && $serial->item_id !== $item->id) {
                throw ValidationException::withMessages([
                    'items' => "Serial number {$serialNumber} sudah terdaftar untuk barang lain.",
                ]);
            }

            $serial->fill([
                'item_id' => $item->id,
                'status' => SerialStatus::Available,
                'customer_id' => null,
                'stock_transaction_item_id' => $transactionItem->id,
                'received_at' => $serial->received_at ?? $transaction->transaction_date,
                'issued_at' => null,
            ])->save();
        }
    }

    /**
     * @param  array<int, string>  $serials
     */
    protected function issueSerials(
        StockTransaction $transaction,
        StockTransactionItem $transactionItem,
        Item $item,
        array $serials,
    ): void {
        $records = SerialNumber::query()
            ->where('item_id', $item->id)
            ->whereIn('serial_number', $serials)
            ->lockForUpdate()
            ->get()
            ->keyBy('serial_number');

        foreach ($serials as $serialNumber) {
            $serial = $records->get($serialNumber);

            if (! $serial || ! $serial->status->isAvailable()) {
                throw ValidationException::withMessages([
                    'items' => "Serial number {$serialNumber} tidak tersedia untuk dikeluarkan.",
                ]);
            }

            $serial->update([
                'status' => SerialStatus::Issued,
                'customer_id' => $transaction->customer_id,
                'stock_transaction_item_id' => $transactionItem->id,
                'issued_at' => $transaction->transaction_date,
            ]);
        }
    }

    /**
     * Mengembalikan kondisi serial/stock sebelum transaksi diubah atau dihapus.
     */
    protected function revertLines(StockTransaction $transaction): void
    {
        $transaction->load('items.serialNumbers');

        foreach ($transaction->items as $line) {
            foreach ($line->serialNumbers as $serial) {
                if ($transaction->direction === TransactionDirection::Out) {
                    $serial->update([
                        'status' => SerialStatus::Available,
                        'customer_id' => null,
                        'stock_transaction_item_id' => null,
                        'issued_at' => null,
                    ]);

                    continue;
                }

                if (! $serial->status->isAvailable()) {
                    throw ValidationException::withMessages([
                        'items' => "Transaksi tidak dapat diubah karena serial {$serial->serial_number} sudah keluar.",
                    ]);
                }

                // Serial yang sudah ada sebelum transaksi ini (mis. dismantle) dipertahankan.
                if ($serial->received_at && $serial->received_at->lt($transaction->transaction_date)) {
                    $serial->update(['stock_transaction_item_id' => null]);

                    continue;
                }

                $serial->delete();
            }

            $line->delete();
        }
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function resolveReference(
        TransactionDirection $direction,
        array $data,
        ?StockTransaction $ignore = null,
    ): string {
        $reference = trim((string) ($data['reference'] ?? ''));

        if ($reference !== '') {
            $exists = StockTransaction::query()
                ->where('reference', $reference)
                ->when($ignore, fn ($query) => $query->whereKeyNot($ignore->getKey()))
                ->exists();

            if ($exists) {
                throw ValidationException::withMessages([
                    'reference' => 'Nomor referensi sudah digunakan.',
                ]);
            }

            return $reference;
        }

        $date = str_replace('-', '', (string) ($data['transaction_date'] ?? now()->toDateString()));
        $prefix = "{$direction->prefix()}-{$date}";
        $sequence = StockTransaction::query()
            ->where('reference', 'like', "{$prefix}-%")
            ->count() + 1;

        do {
            $candidate = sprintf('%s-%04d', $prefix, $sequence++);
        } while (StockTransaction::query()->where('reference', $candidate)->exists());

        return $candidate;
    }

    protected function resolveCustomerId(TransactionActivity $activity, mixed $customerId): ?int
    {
        if (! $activity->requiresCustomer()) {
            return null;
        }

        if (blank($customerId)) {
            throw ValidationException::withMessages([
                'customer_id' => "Pelanggan wajib dipilih untuk aktivitas {$activity->label()}.",
            ]);
        }

        return (int) $customerId;
    }

    protected function assertActivityMatchesDirection(
        TransactionDirection $direction,
        TransactionActivity $activity,
    ): void {
        if ($activity->direction() !== $direction) {
            throw ValidationException::withMessages([
                'activity' => "Aktivitas {$activity->label()} bukan untuk {$direction->label()}.",
            ]);
        }
    }

    /**
     * @return array<int, string>
     */
    protected function normalizeSerials(mixed $serials): array
    {
        if (is_string($serials)) {
            $serials = preg_split('/[\r\n,;]+/', $serials) ?: [];
        }

        return array_values(array_unique(array_filter(
            array_map(fn (mixed $serial): string => trim((string) $serial), (array) $serials),
            fn (string $serial): bool => $serial !== '',
        )));
    }

    /**
     * @return array<int, string>
     */
    protected function defaultRelations(): array
    {
        return ['customer:id,nama_pelanggan,cid,tipe_pelanggan', 'user:id,name', 'items.item', 'items.serialNumbers'];
    }
}
