<?php

namespace App\Enums;

enum TransactionActivity: string
{
    case Dismantle = 'Dismantle';
    case Fpb = 'FPB';
    case Instalasi = 'Instalasi';
    case Maintenance = 'Maintenance';
    case Peminjaman = 'Peminjaman';

    public function label(): string
    {
        return $this->value;
    }

    public function direction(): TransactionDirection
    {
        return match ($this) {
            self::Dismantle, self::Fpb => TransactionDirection::In,
            self::Instalasi, self::Maintenance, self::Peminjaman => TransactionDirection::Out,
        };
    }

    /**
     * Dismantle (retur dari pelanggan) dan seluruh aktivitas keluar
     * selalu terhubung ke pelanggan. FPB tidak terhubung ke pelanggan.
     */
    public function requiresCustomer(): bool
    {
        return $this !== self::Fpb;
    }

    /**
     * @return array<int, string>
     */
    public static function valuesFor(TransactionDirection $direction): array
    {
        return array_values(array_map(
            fn (self $activity): string => $activity->value,
            array_filter(self::cases(), fn (self $activity): bool => $activity->direction() === $direction),
        ));
    }

    /**
     * @return array<int, array{value: string, label: string}>
     */
    public static function optionsFor(TransactionDirection $direction): array
    {
        return array_values(array_map(
            fn (self $activity): array => ['value' => $activity->value, 'label' => $activity->label()],
            array_filter(self::cases(), fn (self $activity): bool => $activity->direction() === $direction),
        ));
    }
}
