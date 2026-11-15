<?php

namespace App\Models;

use App\Enums\BtsStatus;
use Database\Factories\BtsBscFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $nama
 * @property string $alamat
 * @property BtsStatus $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, Customer> $customers
 */
class BtsBsc extends Model
{
    /** @use HasFactory<BtsBscFactory> */
    use HasFactory;

    protected $table = 'bts_bsc';

    protected $fillable = [
        'nama',
        'alamat',
        'status',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => BtsStatus::class,
        ];
    }

    /**
     * @return HasMany<Customer, $this>
     */
    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class);
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
                $query->where('nama', 'like', "%{$term}%")
                    ->orWhere('alamat', 'like', "%{$term}%");
            }),
        );
    }

    /**
     * @param  Builder<$this>  $query
     * @return Builder<$this>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', BtsStatus::Aktif);
    }
}
