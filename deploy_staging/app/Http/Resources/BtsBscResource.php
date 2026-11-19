<?php

namespace App\Http\Resources;

use App\Models\BtsBsc;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin BtsBsc
 */
class BtsBscResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nama' => $this->nama,
            'alamat' => $this->alamat,
            'status' => $this->status->value,
        ];
    }
}
