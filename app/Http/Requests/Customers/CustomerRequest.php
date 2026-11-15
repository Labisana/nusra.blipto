<?php

namespace App\Http\Requests\Customers;

use App\Enums\CustomerStatus;
use App\Enums\CustomerType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

abstract class CustomerRequest extends FormRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'nama_pelanggan' => ['required', 'string', 'max:255'],
            'tipe_pelanggan' => ['required', Rule::enum(CustomerType::class)],
            'status' => ['required', Rule::in(CustomerStatus::values())],
            'bts_bsc_id' => ['nullable', 'integer', Rule::exists('bts_bsc', 'id')],
            'email' => ['nullable', 'email', 'max:255'],
            'jumlah_ap' => ['nullable', 'integer', 'min:0', 'max:10000'],
            'cabang' => ['nullable', 'string', 'max:255'],
            'item' => ['nullable', 'string', 'max:255'],
            'cid' => ['nullable', 'string', 'max:100'],
            'sid' => ['nullable', 'string', 'max:100'],
            'area' => ['nullable', 'string', 'max:255'],
            'sales' => ['nullable', 'string', 'max:255'],
            'alamat' => ['nullable', 'string', 'max:2000'],
            'koordinat' => ['nullable', 'string', 'max:100'],
            'pic' => ['nullable', 'string', 'max:255'],
            'kontak' => ['nullable', 'string', 'max:100'],
            'lastmile' => ['nullable', 'string', 'max:255'],
            'produk' => ['nullable', 'string', 'max:255'],
            'bandwidth_main' => ['nullable', 'string', 'max:100'],
            'bandwidth_2nd' => ['nullable', 'string', 'max:100'],
            'bandwidth_3rd' => ['nullable', 'string', 'max:100'],
            'service' => ['nullable', 'string', 'max:255'],
            'aplikasi' => ['nullable', 'string', 'max:1000'],
            'main_link' => ['nullable', 'string', 'max:255'],
            'backup_link' => ['nullable', 'string', 'max:255'],
            'segment' => ['nullable', 'string', 'max:255'],
            'klasifikasi_pelanggan' => ['nullable', 'string', 'max:255'],
            'jenis_router' => ['nullable', 'string', 'max:255'],
            'onu_ont' => ['nullable', 'string', 'max:255'],
            'odp' => ['nullable', 'string', 'max:255'],
            'pon_port' => ['nullable', 'string', 'max:100'],
            'radio_station' => ['nullable', 'string', 'max:255'],
            'ap_sektor' => ['nullable', 'string', 'max:255'],
            'merk_ap' => ['nullable', 'string', 'max:255'],
            'tipe_ap' => ['nullable', 'string', 'max:255'],
        ];
    }
}
