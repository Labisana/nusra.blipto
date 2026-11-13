<?php

use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login')->name('home');

// Frontend preview gudang (sementara tanpa auth agar bisa ditinjau)
Route::inertia('dashboard', 'Dashboard')->name('dashboard');
Route::inertia('barang-masuk', 'inventory/Incoming')->name('inventory.incoming');
Route::inertia('barang-keluar', 'inventory/Outgoing')->name('inventory.outgoing');
Route::inertia('stok', 'inventory/Stock')->name('inventory.stock');
Route::inertia('laporan', 'inventory/Reports')->name('inventory.reports');
Route::inertia('pelanggan', 'customers/Index')->name('customers.index');
Route::inertia('bts-bsc', 'bts/Index')->name('bts.index');

Route::middleware(['auth', 'verified'])->group(function () {
    // Route terproteksi akan dipindahkan ke sini setelah integrasi backend & auth
});

require __DIR__.'/settings.php';
