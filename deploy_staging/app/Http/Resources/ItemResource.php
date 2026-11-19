<?php

namespace App\Http\Resources;

use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Item
 */
class ItemResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'sku' => $this->sku,
            'name' => $this->name,
            'type' => $this->type->value,
            'type_label' => $this->type->label(),
            'unit' => $this->unit,
            'location' => $this->location,
            'min_stock' => $this->min_stock,
            'stock_in' => (int) ($this->stock_in ?? 0),
            'stock_out' => (int) ($this->stock_out ?? 0),
            'remaining' => $this->remaining,
            'serials_available' => $this->whenLoaded(
                'availableSerialNumbers',
                fn (): array => $this->availableSerialNumbers->pluck('serial_number')->all(),
            ),
            'serials' => $this->whenLoaded(
                'serialNumbers',
                fn (): mixed => SerialNumberResource::collection($this->serialNumbers),
            ),
        ];
    }
}
