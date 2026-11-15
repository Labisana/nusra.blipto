<?php

namespace App\Enums;

enum CustomerType: string
{
    case Busol = 'Busol';
    case Broadband = 'Broadband';

    public function label(): string
    {
        return $this->value;
    }
}
