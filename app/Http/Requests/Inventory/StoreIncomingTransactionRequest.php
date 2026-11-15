<?php

namespace App\Http\Requests\Inventory;

use App\Enums\TransactionDirection;

class StoreIncomingTransactionRequest extends StockTransactionRequest
{
    protected function direction(): TransactionDirection
    {
        return TransactionDirection::In;
    }
}
