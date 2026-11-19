<?php

namespace App\Http\Resources;

use App\Models\StockTransactionItem;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin StockTransactionItem
 */
class StockTransactionItemResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'item_id' => $this->item_id,
            'item_name' => $this->whenLoaded('item', fn (): string => $this->item->name),
            'item_type' => $this->whenLoaded('item', fn (): string => $this->item->type->value),
            'item_type_label' => $this->whenLoaded('item', fn (): string => $this->item->type->label()),
            'quantity' => $this->quantity,
            'serials' => $this->whenLoaded(
                'serialNumbers',
                fn (): array => $this->serialNumbers->pluck('serial_number')->all(),
            ),
        ];
    }
}
