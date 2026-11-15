<?php

namespace App\Services\Inventory;

use App\Enums\TransactionDirection;
use App\Models\Customer;
use App\Models\Item;
use App\Models\StockTransaction;
use Illuminate\Support\Collection;

class StockSummaryService
{
    /**
     * @param  array{search?: string|null, type?: string|null}  $filters
     * @return Collection<int, Item>
     */
    public function items(array $filters = []): Collection
    {
        return Item::query()
            ->withStockTotals()
            ->search($filters['search'] ?? null)
            ->ofType($filters['type'] ?? null)
            ->orderBy('name')
            ->get();
    }

    /**
     * @param  array{search?: string|null, type?: string|null}  $filters
     * @return Collection<int, array<string, mixed>>
     */
    public function rows(array $filters = []): Collection
    {
        return $this->items($filters)->map($this->row(...));
    }

    /**
     * @return array<string, mixed>
     */
    protected function row(Item $item): array
    {
        return [
            'id' => $item->id,
            'sku' => $item->sku,
            'item' => $item->name,
            'type' => $item->type->value,
            'type_label' => $item->type->label(),
            'in' => (int) ($item->stock_in ?? 0),
            'out' => (int) ($item->stock_out ?? 0),
            'remaining' => $item->remaining,
            'unit' => $item->unit,
            'location' => $item->location,
            'min_stock' => $item->min_stock,
        ];
    }

    /**
     * @return Collection<int, Item>
     */
    public function lowStock(int $limit = 5): Collection
    {
        return $this->items()
            ->filter(fn (Item $item): bool => $item->min_stock > 0 && $item->remaining <= $item->min_stock)
            ->sortBy('remaining')
            ->take($limit)
            ->values();
    }

    /**
     * @return array<string, int>
     */
    public function dashboardStats(): array
    {
        return [
            'incoming_this_month' => StockTransaction::query()
                ->direction(TransactionDirection::In)
                ->whereMonth('transaction_date', now()->month)
                ->whereYear('transaction_date', now()->year)
                ->count(),
            'outgoing_this_month' => StockTransaction::query()
                ->direction(TransactionDirection::Out)
                ->whereMonth('transaction_date', now()->month)
                ->whereYear('transaction_date', now()->year)
                ->count(),
            'total_remaining' => (int) $this->items()->sum(fn (Item $item): int => $item->remaining),
            'active_customers' => Customer::query()->active()->count(),
        ];
    }
}
