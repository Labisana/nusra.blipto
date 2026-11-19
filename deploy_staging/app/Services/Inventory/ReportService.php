<?php

namespace App\Services\Inventory;

use App\Enums\ItemType;
use App\Enums\TransactionDirection;
use App\Models\SerialNumber;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use stdClass;

class ReportService
{
    public function __construct(private readonly StockSummaryService $stockSummary) {}

    /**
     * @param  array{from?: string|null, to?: string|null, item_type?: string|null, customer_id?: int|null}  $filters
     * @return Collection<int, array<string, mixed>>
     */
    public function incoming(array $filters = [], ?int $limit = null): Collection
    {
        return $this->lines(TransactionDirection::In, $filters, $limit);
    }

    /**
     * @param  array{from?: string|null, to?: string|null, item_type?: string|null, customer_id?: int|null}  $filters
     * @return Collection<int, array<string, mixed>>
     */
    public function outgoing(array $filters = [], ?int $limit = null): Collection
    {
        return $this->lines(TransactionDirection::Out, $filters, $limit);
    }

    /**
     * @param  array{from?: string|null, to?: string|null, item_type?: string|null, customer_id?: int|null}  $filters
     * @return Collection<int, array<string, mixed>>
     */
    public function stock(array $filters = []): Collection
    {
        return $this->stockSummary->rows([
            'type' => $filters['item_type'] ?? null,
        ]);
    }

    /**
     * @param  array{from?: string|null, to?: string|null, item_type?: string|null, customer_id?: int|null}  $filters
     * @return Collection<int, array<string, mixed>>
     */
    protected function lines(TransactionDirection $direction, array $filters, ?int $limit = null): Collection
    {
        $from = $filters['from'] ?? null;
        $to = $filters['to'] ?? null;
        $itemType = $filters['item_type'] ?? null;
        $customerId = $filters['customer_id'] ?? null;

        $query = DB::table('stock_transaction_items as lines')
            ->join('stock_transactions as transactions', 'transactions.id', '=', 'lines.stock_transaction_id')
            ->join('items', 'items.id', '=', 'lines.item_id')
            ->leftJoin('customers', 'customers.id', '=', 'transactions.customer_id')
            ->leftJoin('users', 'users.id', '=', 'transactions.user_id')
            ->where('transactions.direction', $direction->value)
            ->when(
                filled($from),
                fn (Builder $query): Builder => $query->whereDate('transactions.transaction_date', '>=', $from),
            )
            ->when(
                filled($to),
                fn (Builder $query): Builder => $query->whereDate('transactions.transaction_date', '<=', $to),
            )
            ->when(
                filled($itemType),
                fn (Builder $query): Builder => $query->where('items.type', $itemType),
            )
            ->when(
                filled($customerId),
                fn (Builder $query): Builder => $query->where('transactions.customer_id', $customerId),
            )
            ->select([
                'lines.id as id',
                'lines.quantity as quantity',
                'items.name as item_name',
                'items.type as item_type',
                'transactions.reference as reference',
                'transactions.activity as activity',
                'transactions.transaction_date as transaction_date',
                'customers.nama_pelanggan as customer_name',
                'customers.tipe_pelanggan as customer_type',
                'users.name as user_name',
            ])
            ->orderByDesc('transactions.transaction_date')
            ->orderByDesc('transactions.id')
            ->orderBy('items.name');

        if ($limit !== null) {
            $query->limit($limit);
        }

        $rows = $query->get();
        $serialNumbers = $this->serialNumberMap($rows->pluck('id')->all());

        return $rows->map(fn (stdClass $row): array => $this->lineRow($row, $serialNumbers));
    }

    /**
     * @param  array<int, string>  $serialNumbers  Dipetakan dari id baris transaksi.
     * @return array<string, mixed>
     */
    protected function lineRow(stdClass $row, array $serialNumbers): array
    {
        $id = (int) $row->id;

        return [
            'id' => $id,
            'date' => Carbon::parse((string) $row->transaction_date)->toDateString(),
            'ref' => $row->reference,
            'activity' => $row->activity,
            'customer' => $row->customer_name ?? '-',
            'tipe_pelanggan' => $row->customer_type ?? '-',
            'item' => $row->item_name,
            'type' => ItemType::from((string) $row->item_type)->label(),
            'type_value' => $row->item_type,
            'qty' => (int) $row->quantity,
            'serials' => $serialNumbers[$id] ?? '-',
            'user' => $row->user_name ?? 'Sistem',
        ];
    }

    /**
     * Menggabungkan serial number per baris transaksi dalam satu query.
     *
     * @param  array<int, mixed>  $lineIds
     * @return array<int, string>
     */
    protected function serialNumberMap(array $lineIds): array
    {
        $map = [];

        $serials = SerialNumber::query()
            ->whereIn('stock_transaction_item_id', $lineIds)
            ->orderBy('serial_number')
            ->get(['stock_transaction_item_id', 'serial_number']);

        foreach ($serials as $serial) {
            $lineId = (int) $serial->stock_transaction_item_id;
            $map[$lineId] = isset($map[$lineId])
                ? $map[$lineId].', '.$serial->serial_number
                : $serial->serial_number;
        }

        return $map;
    }
}
