<?php

namespace App\Http\Resources;

use App\Models\StockTransaction;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin StockTransaction
 */
class StockTransactionResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'reference' => $this->reference,
            'direction' => $this->direction->value,
            'activity' => $this->activity->value,
            'transaction_date' => $this->transaction_date->toDateString(),
            'note' => $this->note,
            'customer_id' => $this->customer_id,
            'customer' => $this->whenLoaded('customer', fn (): ?array => $this->customer ? [
                'id' => $this->customer->id,
                'nama_pelanggan' => $this->customer->nama_pelanggan,
                'cid' => $this->customer->cid,
                'tipe_pelanggan' => $this->customer->tipe_pelanggan->value,
            ] : null),
            'user' => $this->whenLoaded('user', fn (): ?array => $this->user ? [
                'id' => $this->user->id,
                'name' => $this->user->name,
            ] : null),
            'items' => $this->whenLoaded(
                'items',
                fn (): mixed => StockTransactionItemResource::collection($this->items),
            ),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
