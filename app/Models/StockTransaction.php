<?php

namespace App\Models;

use App\Enums\TransactionActivity;
use App\Enums\TransactionDirection;
use App\Enums\TransactionStatus;
use Database\Factories\StockTransactionFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $reference
 * @property TransactionDirection $direction
 * @property TransactionActivity $activity
 * @property int|null $customer_id
 * @property Carbon $transaction_date
 * @property string|null $note
 * @property int|null $user_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Customer|null $customer
 * @property-read User|null $user
 * @property-read Collection<int, StockTransactionItem> $items
 * @property-read Collection<int, SerialNumber> $serialNumbers
 */
class StockTransaction extends Model
{
    /** @use HasFactory<StockTransactionFactory> */
    use HasFactory;

    protected $fillable = [
        'reference',
        'direction',
        'activity',
        'status',
        'customer_id',
        'transaction_date',
        'note',
        'user_id',
        'approved_by',
        'approved_at',
        'rejection_note',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'direction' => TransactionDirection::class,
            'activity' => TransactionActivity::class,
            'status' => TransactionStatus::class,
            'transaction_date' => 'date',
            'approved_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<Customer, $this>
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function isPending(): bool
    {
        return $this->status === TransactionStatus::Pending;
    }

    public function isApproved(): bool
    {
        return $this->status === TransactionStatus::Approved;
    }

    public function isRejected(): bool
    {
        return $this->status === TransactionStatus::Rejected;
    }

    /**
     * @return HasMany<StockTransactionItem, $this>
     */
    public function items(): HasMany
    {
        return $this->hasMany(StockTransactionItem::class, 'stock_transaction_id');
    }

    /**
     * @return HasManyThrough<SerialNumber, StockTransactionItem, $this>
     */
    public function serialNumbers(): HasManyThrough
    {
        return $this->hasManyThrough(
            SerialNumber::class,
            StockTransactionItem::class,
            'stock_transaction_id',
            'stock_transaction_item_id',
            'id',
            'id',
        );
    }

    /**
     * @param  Builder<$this>  $query
     * @return Builder<$this>
     */
    public function scopeDirection(Builder $query, TransactionDirection $direction): Builder
    {
        return $query->where('direction', $direction);
    }

    /**
     * @param  Builder<$this>  $query
     * @return Builder<$this>
     */
    public function scopeBetweenDates(Builder $query, ?string $from, ?string $to): Builder
    {
        return $query
            ->when(filled($from), fn (Builder $query): Builder => $query->whereDate('transaction_date', '>=', $from))
            ->when(filled($to), fn (Builder $query): Builder => $query->whereDate('transaction_date', '<=', $to));
    }

    /**
     * @param  Builder<$this>  $query
     * @return Builder<$this>
     */
    public function scopeForCustomer(Builder $query, ?int $customerId): Builder
    {
        return $query->when(filled($customerId), fn (Builder $query): Builder => $query->where('customer_id', $customerId));
    }

    /**
     * @param  Builder<$this>  $query
     * @return Builder<$this>
     */
    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', TransactionStatus::Approved);
    }

    /**
     * @param  Builder<$this>  $query
     * @return Builder<$this>
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', TransactionStatus::Pending);
    }

    /**
     * @return array<string, mixed>
     */
    public function toWhatsAppReport(): array
    {
        $this->loadMissing(['customer', 'user', 'items.item', 'items.serialNumbers']);

        return [
            'id' => $this->id,
            'reference' => $this->reference,
            'direction' => $this->direction->value,
            'direction_label' => $this->direction === TransactionDirection::In ? 'BARANG MASUK' : 'BARANG KELUAR',
            'activity' => $this->activity->value,
            'date' => $this->transaction_date->format('d/m/Y'),
            'user_name' => $this->user?->name ?? 'Teknisi',
            'customer_name' => $this->customer?->nama_pelanggan,
            'customer_type' => $this->customer?->tipe_pelanggan?->value,
            'status' => $this->status->value,
            'status_label' => $this->status->label(),
            'note' => $this->note,
            'items' => $this->items->map(fn ($line) => [
                'name' => $line->item->name,
                'quantity' => $line->quantity,
                'unit' => $line->item->unit,
                'serials' => $line->relationLoaded('serialNumbers') && $line->serialNumbers->isNotEmpty()
                    ? $line->serialNumbers->pluck('serial_number')->all()
                    : ($line->serials_json ?? []),
            ])->values()->all(),
        ];
    }
}
