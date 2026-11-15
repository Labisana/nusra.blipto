<?php

namespace App\Models;

use App\Enums\CustomerStatus;
use App\Enums\CustomerType;
use Database\Factories\CustomerFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string|null $cabang
 * @property string|null $item
 * @property CustomerType $tipe_pelanggan
 * @property string|null $cid
 * @property string|null $sid
 * @property CustomerStatus $status
 * @property string $nama_pelanggan
 * @property string|null $area
 * @property string|null $sales
 * @property string|null $alamat
 * @property string|null $koordinat
 * @property string|null $pic
 * @property string|null $kontak
 * @property string|null $email
 * @property string|null $lastmile
 * @property string|null $produk
 * @property string|null $bandwidth_main
 * @property string|null $bandwidth_2nd
 * @property string|null $bandwidth_3rd
 * @property string|null $service
 * @property string|null $aplikasi
 * @property string|null $main_link
 * @property string|null $backup_link
 * @property string|null $segment
 * @property string|null $klasifikasi_pelanggan
 * @property string $visit
 * @property int|null $bts_bsc_id
 * @property string|null $jenis_router
 * @property string|null $onu_ont
 * @property string|null $odp
 * @property string|null $pon_port
 * @property string|null $radio_station
 * @property string|null $ap_sektor
 * @property int|null $jumlah_ap
 * @property string|null $merk_ap
 * @property string|null $tipe_ap
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read BtsBsc|null $btsBsc
 * @property-read Collection<int, StockTransaction> $stockTransactions
 * @property-read Collection<int, SerialNumber> $serialNumbers
 */
class Customer extends Model
{
    /** @use HasFactory<CustomerFactory> */
    use HasFactory;

    protected $fillable = [
        'cabang',
        'item',
        'tipe_pelanggan',
        'cid',
        'sid',
        'status',
        'nama_pelanggan',
        'area',
        'sales',
        'alamat',
        'koordinat',
        'pic',
        'kontak',
        'email',
        'lastmile',
        'produk',
        'bandwidth_main',
        'bandwidth_2nd',
        'bandwidth_3rd',
        'service',
        'aplikasi',
        'main_link',
        'backup_link',
        'segment',
        'klasifikasi_pelanggan',
        'visit',
        'bts_bsc_id',
        'jenis_router',
        'onu_ont',
        'odp',
        'pon_port',
        'radio_station',
        'ap_sektor',
        'jumlah_ap',
        'merk_ap',
        'tipe_ap',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'tipe_pelanggan' => CustomerType::class,
            'status' => CustomerStatus::class,
            'jumlah_ap' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Customer $customer): void {
            $customer->visit = $customer->visitForClassification();
        });
    }

    /**
     * Platinum dikunjungi setiap bulan, klasifikasi lain setiap 3 bulan.
     */
    public function visitForClassification(): string
    {
        $classification = strtolower(trim((string) $this->klasifikasi_pelanggan));

        return $classification === 'platinum' ? '/1Bulan' : '/3Bulan';
    }

    /**
     * @return BelongsTo<BtsBsc, $this>
     */
    public function btsBsc(): BelongsTo
    {
        return $this->belongsTo(BtsBsc::class);
    }

    /**
     * @return HasMany<StockTransaction, $this>
     */
    public function stockTransactions(): HasMany
    {
        return $this->hasMany(StockTransaction::class);
    }

    /**
     * @return HasMany<SerialNumber, $this>
     */
    public function serialNumbers(): HasMany
    {
        return $this->hasMany(SerialNumber::class);
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
                $query->where('nama_pelanggan', 'like', "%{$term}%")
                    ->orWhere('cid', 'like', "%{$term}%")
                    ->orWhere('sid', 'like', "%{$term}%")
                    ->orWhere('cabang', 'like', "%{$term}%")
                    ->orWhere('area', 'like', "%{$term}%");
            }),
        );
    }

    /**
     * @param  Builder<$this>  $query
     * @return Builder<$this>
     */
    public function scopeOfType(Builder $query, ?string $type): Builder
    {
        return $query->when(filled($type), fn (Builder $query): Builder => $query->where('tipe_pelanggan', $type));
    }

    /**
     * @param  Builder<$this>  $query
     * @return Builder<$this>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', CustomerStatus::Aktif);
    }
}
