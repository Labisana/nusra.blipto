<?php

namespace App\Models;

use App\Enums\SerialStatus;
use Database\Factories\SerialNumberFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $item_id
 * @property string $serial_number
 * @property SerialStatus $status
 * @property int|null $customer_id
 * @property int|null $stock_transaction_item_id
 * @property Carbon|null $received_at
 * @property Carbon|null $issued_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Item $item
 * @property-read Customer|null $customer
 * @property-read StockTransactionItem|null $transactionItem
 */
class SerialNumber extends Model
{
    /** @use HasFactory<SerialNumberFactory> */
    use HasFactory;

    protected $fillable = [
        'item_id',
        'serial_number',
        'status',
        'customer_id',
        'stock_transaction_item_id',
        'received_at',
        'issued_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => SerialStatus::class,
            'received_at' => 'date',
            'issued_at' => 'date',
        ];
    }

    /**
     * @return BelongsTo<Item, $this>
     */
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    /**
     * @return BelongsTo<Customer, $this>
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * @return BelongsTo<StockTransactionItem, $this>
     */
    public function transactionItem(): BelongsTo
    {
        return $this->belongsTo(StockTransactionItem::class, 'stock_transaction_item_id');
    }

    /**
     * @param  Builder<$this>  $query
     * @return Builder<$this>
     */
    public function scopeAvailable(Builder $query): Builder
    {
        return $query->where('status', SerialStatus::Available);
    }

    /**
     * @param  Builder<$this>  $query
     * @return Builder<$this>
     */
    public function scopeIssued(Builder $query): Builder
    {
        return $query->where('status', SerialStatus::Issued);
    }
}
