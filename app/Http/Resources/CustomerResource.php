<?php

namespace App\Http\Resources;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Customer
 */
class CustomerResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'cabang' => $this->cabang,
            'item' => $this->item,
            'tipe_pelanggan' => $this->tipe_pelanggan->value,
            'cid' => $this->cid,
            'sid' => $this->sid,
            'status' => $this->status->value,
            'nama_pelanggan' => $this->nama_pelanggan,
            'area' => $this->area,
            'sales' => $this->sales,
            'alamat' => $this->alamat,
            'koordinat' => $this->koordinat,
            'pic' => $this->pic,
            'kontak' => $this->kontak,
            'email' => $this->email,
            'lastmile' => $this->lastmile,
            'produk' => $this->produk,
            'bandwidth_main' => $this->bandwidth_main,
            'bandwidth_2nd' => $this->bandwidth_2nd,
            'bandwidth_3rd' => $this->bandwidth_3rd,
            'service' => $this->service,
            'aplikasi' => $this->aplikasi,
            'main_link' => $this->main_link,
            'backup_link' => $this->backup_link,
            'segment' => $this->segment,
            'klasifikasi_pelanggan' => $this->klasifikasi_pelanggan,
            'visit' => $this->visit,
            'bts_bsc_id' => $this->bts_bsc_id,
            'bts_bsc' => $this->whenLoaded('btsBsc', fn (): BtsBscResource => new BtsBscResource($this->btsBsc)),
            'jenis_router' => $this->jenis_router,
            'onu_ont' => $this->onu_ont,
            'odp' => $this->odp,
            'pon_port' => $this->pon_port,
            'radio_station' => $this->radio_station,
            'ap_sektor' => $this->ap_sektor,
            'jumlah_ap' => $this->jumlah_ap,
            'merk_ap' => $this->merk_ap,
            'tipe_ap' => $this->tipe_ap,
        ];
    }
}
