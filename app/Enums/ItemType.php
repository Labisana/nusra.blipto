<?php

namespace App\Enums;

enum ItemType: string
{
    case Serial = 'serial';
    case NonSerial = 'non-serial';

    public function label(): string
    {
        return match ($this) {
            self::Serial => 'Serial Number',
            self::NonSerial => 'Non-Serial',
        };
    }

    public function isSerial(): bool
    {
        return $this === self::Serial;
    }

    /**
     * @return array<int, array{value: string, label: string}>
     */
    public static function options(): array
    {
        return array_map(
            fn (self $type): array => ['value' => $type->value, 'label' => $type->label()],
            self::cases(),
        );
    }
}
