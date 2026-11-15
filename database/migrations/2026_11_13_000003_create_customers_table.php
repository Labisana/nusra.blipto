<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();

            // Identitas & layanan
            $table->string('cabang')->nullable();
            $table->string('item')->nullable();
            $table->string('tipe_pelanggan')->default('Busol')->index();
            $table->string('cid')->nullable()->index();
            $table->string('sid')->nullable()->index();
            $table->string('status')->default('Aktif')->index();
            $table->string('nama_pelanggan')->index();
            $table->string('area')->nullable();
            $table->string('sales')->nullable();
            $table->text('alamat')->nullable();
            $table->string('koordinat')->nullable();
            $table->string('pic')->nullable();
            $table->string('kontak')->nullable();
            $table->string('email')->nullable();

            // Layanan & bandwidth
            $table->string('lastmile')->nullable();
            $table->string('produk')->nullable();
            $table->string('bandwidth_main')->nullable();
            $table->string('bandwidth_2nd')->nullable();
            $table->string('bandwidth_3rd')->nullable();
            $table->string('service')->nullable();
            $table->text('aplikasi')->nullable();
            $table->string('main_link')->nullable();
            $table->string('backup_link')->nullable();
            $table->string('segment')->nullable();
            $table->string('klasifikasi_pelanggan')->nullable()->index();
            $table->string('visit')->default('/3Bulan');

            // Perangkat & jaringan
            $table->foreignId('bts_bsc_id')->nullable()->constrained('bts_bsc')->nullOnDelete();
            $table->string('jenis_router')->nullable();
            $table->string('onu_ont')->nullable();
            $table->string('odp')->nullable();
            $table->string('pon_port')->nullable();
            $table->string('radio_station')->nullable();
            $table->string('ap_sektor')->nullable();
            $table->unsignedInteger('jumlah_ap')->nullable();
            $table->string('merk_ap')->nullable();
            $table->string('tipe_ap')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
