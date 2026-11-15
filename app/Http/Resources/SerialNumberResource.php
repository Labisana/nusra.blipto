<?php

namespace App\Http\Resources;

use App\Models\SerialNumber;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin SerialNumber
 */
class SerialNumberResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'sn' => $this->serial_number,
            'status' => $this->status->label(),
            'status_value' => $this->status->value,
            'item_id' => $this->item_id,
            'item_name' => $this->whenLoaded('item', fn (): string => $this->item->name),
            'customer_name' => $this->whenLoaded('customer', fn (): ?string => $this->customer?->nama_pelanggan),
            'received_at' => $this->received_at?->toDateString(),
            'issued_at' => $this->issued_at?->toDateString(),
        ];
    }
}
