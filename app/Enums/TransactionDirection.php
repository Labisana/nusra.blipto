<?php

namespace App\Enums;

enum TransactionDirection: string
{
    case In = 'in';
    case Out = 'out';

    public function label(): string
    {
        return match ($this) {
            self::In => 'Barang Masuk',
            self::Out => 'Barang Keluar',
        };
    }

    public function prefix(): string
    {
        return match ($this) {
            self::In => 'IN',
            self::Out => 'OUT',
        };
    }
}
