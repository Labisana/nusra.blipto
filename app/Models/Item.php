<?php

namespace App\Models;

use App\Enums\ItemType;
use App\Enums\SerialStatus;
use App\Enums\TransactionDirection;
use App\Enums\TransactionStatus;
use Database\Factories\ItemFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $sku
 * @property string $name
 * @property ItemType $type
 * @property string $unit
 * @property string|null $location
 * @property int $min_stock
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read int|null $stock_in
 * @property-read int|null $stock_out
 * @property-read int $remaining
 * @property-read Collection<int, SerialNumber> $serialNumbers
 * @property-read Collection<int, StockTransactionItem> $transactionItems
 */
class Item extends Model
{
    /** @use HasFactory<ItemFactory> */
    use HasFactory;

    protected $fillable = [
        'sku',
        'name',
        'type',
        'unit',
        'location',
        'min_stock',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'type' => ItemType::class,
            'min_stock' => 'integer',
        ];
    }

    /**
     * @return HasMany<SerialNumber, $this>
     */
    public function serialNumbers(): HasMany
    {
        return $this->hasMany(SerialNumber::class);
    }

    /**
     * @return HasMany<SerialNumber, $this>
     */
    public function availableSerialNumbers(): HasMany
    {
        return $this->serialNumbers()->where('status', SerialStatus::Available);
    }

    /**
     * @return HasMany<StockTransactionItem, $this>
     */
    public function transactionItems(): HasMany
    {
        return $this->hasMany(StockTransactionItem::class);
    }

    /**
     * @return HasManyThrough<StockTransaction, StockTransactionItem, $this>
     */
    public function stockTransactions(): HasManyThrough
    {
        return $this->hasManyThrough(
            StockTransaction::class,
            StockTransactionItem::class,
            'item_id',
            'id',
            'id',
            'stock_transaction_id',
        );
    }

    public function isSerial(): bool
    {
        return $this->type->isSerial();
    }

    /**
     * Menambahkan agregat total masuk/keluar sebagai stock_in & stock_out.
     *
     * @param  Builder<$this>  $query
     * @return Builder<$this>
     */
    public function scopeWithStockTotals(Builder $query): Builder
    {
        return $query
            ->withSum(
                ['transactionItems as stock_in' => fn (Builder $query): Builder => $query->whereHas(
                    'transaction',
                    fn (Builder $query): Builder => $query->where('direction', TransactionDirection::In)
                        ->where('status', TransactionStatus::Approved),
                )],
                'quantity',
            )
            ->withSum(
                ['transactionItems as stock_out' => fn (Builder $query): Builder => $query->whereHas(
                    'transaction',
                    fn (Builder $query): Builder => $query->where('direction', TransactionDirection::Out)
                        ->where('status', TransactionStatus::Approved),
                )],
                'quantity',
            );
    }

    public function getRemainingAttribute(): int
    {
        return (int) ($this->stock_in ?? 0) - (int) ($this->stock_out ?? 0);
    }

    /**
     * @param  Builder<$this>  $query
     * @return Builder<$this>
     */
    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        return $query->when(
            filled($term),
            fn (Builder $query): Builder => $query->where(function (Builder $query) use ($term): void {
                $query->where('sku', 'like', "%{$term}%")
                    ->orWhere('name', 'like', "%{$term}%");
            }),
        );
    }

    /**
     * @param  Builder<$this>  $query
     * @return Builder<$this>
     */
    public function scopeOfType(Builder $query, ?string $type): Builder
    {
        return $query->when(filled($type), fn (Builder $query): Builder => $query->where('type', $type));
    }
}
