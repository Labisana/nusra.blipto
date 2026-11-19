<?php

namespace App\Enums;

enum SerialStatus: string
{
    case Available = 'available';
    case Issued = 'issued';

    public function label(): string
    {
        return match ($this) {
            self::Available => 'Tersedia',
            self::Issued => 'Keluar',
        };
    }

    public function isAvailable(): bool
    {
        return $this === self::Available;
    }
}
