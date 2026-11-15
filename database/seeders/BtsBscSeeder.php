<?php

namespace Database\Seeders;

use App\Enums\BtsStatus;
use App\Models\BtsBsc;
use Illuminate\Database\Seeder;

class BtsBscSeeder extends Seeder
{
    public function run(): void
    {
        $records = [
            ['nama' => 'BTS-MTR-01', 'alamat' => 'Jl. Pejanggik No. 10, Mataram', 'status' => BtsStatus::Aktif],
            ['nama' => 'BTS-MTR-02', 'alamat' => 'Jl. Selaparang No. 22, Cakranegara', 'status' => BtsStatus::Aktif],
            ['nama' => 'BTS-PRA-03', 'alamat' => 'Jl. Gajah Mada No. 5, Praya', 'status' => BtsStatus::Aktif],
            ['nama' => 'BSC-LOM-01', 'alamat' => 'Jl. Raya Senggigi, Lombok Barat', 'status' => BtsStatus::NonAktif],
        ];

        foreach ($records as $record) {
            BtsBsc::query()->updateOrCreate(['nama' => $record['nama']], $record);
        }
    }
}
