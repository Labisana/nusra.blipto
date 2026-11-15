<?php

namespace App\Enums;

enum Role: string
{
    case Spv = 'spv';
    case Admin = 'admin';
    case Teknisi = 'teknisi';

    public function label(): string
    {
        return match ($this) {
            self::Spv => 'SPV',
            self::Admin => 'Admin',
            self::Teknisi => 'Teknisi',
        };
    }

    /**
     * SPV dan Admin dapat menambah, mengubah, dan menghapus data master.
     */
    public function canManage(): bool
    {
        return $this !== self::Teknisi;
    }

    /**
     * Teknisi hanya boleh membuat transaksi barang masuk/keluar.
     */
    public function canViewReports(): bool
    {
        return $this !== self::Teknisi;
    }

    /**
     * @return array<int, array{value: string, label: string}>
     */
    public static function options(): array
    {
        return array_map(
            fn (self $role): array => ['value' => $role->value, 'label' => $role->label()],
            self::cases(),
        );
    }
}
