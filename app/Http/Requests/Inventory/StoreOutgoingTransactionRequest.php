<?php

namespace App\Http\Requests\Inventory;

use App\Enums\TransactionDirection;

class StoreOutgoingTransactionRequest extends StockTransactionRequest
{
    protected function direction(): TransactionDirection
    {
        return TransactionDirection::Out;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            ...parent::rules(),
            'customer_id' => ['required', 'integer', 'exists:customers,id'],
        ];
    }
}
