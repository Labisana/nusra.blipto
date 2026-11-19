<?php

namespace App\Enums;

enum CustomerStatus: string
{
    case Aktif = 'Aktif';
    case Suspend = 'Suspend';
    case NonAktif = 'Non Aktif';

    public function label(): string
    {
        return $this->value;
    }

    /**
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_map(fn (self $status): string => $status->value, self::cases());
    }
}
