<?php

namespace Database\Seeders;

use App\Enums\CustomerStatus;
use App\Enums\CustomerType;
use App\Models\BtsBsc;
use App\Models\Customer;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $bts = BtsBsc::query()->pluck('id', 'nama');

        $customers = [
            [
                'cabang' => 'Mataram',
                'item' => 'Internet Dedicated',
                'tipe_pelanggan' => CustomerType::Busol,
                'cid' => 'C-001',
                'sid' => 'S-1001',
                'status' => CustomerStatus::Aktif,
                'nama_pelanggan' => 'PT Nusra Net - Cab. Mataram',
                'area' => 'Mataram Kota',
                'sales' => 'Rizky',
                'alamat' => 'Jl. Pejanggik No. 10, Mataram',
                'koordinat' => '-8.5833, 116.1167',
                'pic' => 'Ahmad Fauzi',
                'kontak' => '0812-3456-7001',
                'email' => 'noc.mataram@nusranet.id',
                'lastmile' => 'Fiber Optic',
                'produk' => 'Metro-E',
                'bandwidth_main' => '100 Mbps',
                'bandwidth_2nd' => '50 Mbps',
                'service' => 'Managed Service',
                'aplikasi' => 'ERP, CCTV',
                'main_link' => 'FO-MTR-01',
                'backup_link' => 'Radio-MTR-02',
                'segment' => 'Enterprise',
                'klasifikasi_pelanggan' => 'Platinum',
                'bts_bsc_id' => $bts['BTS-MTR-01'] ?? null,
                'jenis_router' => 'Mikrotik CCR1009',
                'onu_ont' => 'ZTE F609 (SN: F609-A01)',
                'odp' => 'ODP-MTR-010',
                'pon_port' => '1/1/8',
                'jumlah_ap' => 0,
            ],
            [
                'cabang' => 'Praya',
                'item' => 'Internet Broadband',
                'tipe_pelanggan' => CustomerType::Broadband,
                'cid' => 'C-002',
                'sid' => 'S-1002',
                'status' => CustomerStatus::Aktif,
                'nama_pelanggan' => 'PT Nusra Net - Cab. Praya',
                'area' => 'Lombok Tengah',
                'sales' => 'Sinta',
                'alamat' => 'Jl. Gajah Mada No. 5, Praya',
                'koordinat' => '-8.7054, 116.2704',
                'pic' => 'Lalu Hendra',
                'kontak' => '0813-9988-1122',
                'email' => 'noc.praya@nusranet.id',
                'lastmile' => 'Wireless',
                'produk' => 'Broadband',
                'bandwidth_main' => '50 Mbps',
                'service' => 'Best Effort',
                'aplikasi' => 'POS',
                'main_link' => 'RAD-PRA-01',
                'segment' => 'SME',
                'klasifikasi_pelanggan' => 'Silver',
                'bts_bsc_id' => $bts['BTS-PRA-03'] ?? null,
                'jenis_router' => 'Mikrotik RB750',
                'radio_station' => 'RS-PRA-01',
                'ap_sektor' => 'Sektor 2',
                'jumlah_ap' => 2,
                'merk_ap' => 'Ubiquiti',
                'tipe_ap' => 'LiteBeam AC',
            ],
            [
                'cabang' => 'Mataram',
                'item' => 'VPN + Internet',
                'tipe_pelanggan' => CustomerType::Busol,
                'cid' => 'C-003',
                'sid' => 'S-1003',
                'status' => CustomerStatus::Suspend,
                'nama_pelanggan' => 'CV Sinar Lombok',
                'area' => 'Cakranegara',
                'sales' => 'Rizky',
                'alamat' => 'Jl. Selaparang No. 22, Cakranegara',
                'koordinat' => '-8.5889, 116.1300',
                'pic' => 'Baiq Nur',
                'kontak' => '0877-2200-3344',
                'email' => 'it@sinar-lombok.co.id',
                'lastmile' => 'Fiber Optic',
                'produk' => 'L2VPN',
                'bandwidth_main' => '20 Mbps',
                'service' => 'Managed Service',
                'aplikasi' => 'VPN Kantor',
                'main_link' => 'FO-CKR-04',
                'segment' => 'SOHO',
                'klasifikasi_pelanggan' => 'Bronze',
                'bts_bsc_id' => $bts['BTS-MTR-02'] ?? null,
                'jenis_router' => 'Mikrotik RB450',
                'onu_ont' => 'ZTE F609 (SN: F609-A11)',
                'odp' => 'ODP-CKR-004',
                'pon_port' => '1/2/3',
                'jumlah_ap' => 0,
            ],
        ];

        foreach ($customers as $customer) {
            Customer::query()->updateOrCreate(['cid' => $customer['cid']], $customer);
        }
    }
}
