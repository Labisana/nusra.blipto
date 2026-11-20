<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Inventory\BtsBscController;
use App\Http\Controllers\Inventory\CustomerController;
use App\Http\Controllers\Inventory\IncomingTransactionController;
use App\Http\Controllers\Inventory\ItemController;
use App\Http\Controllers\Inventory\OutgoingTransactionController;
use App\Http\Controllers\Inventory\ReportController;
use App\Http\Controllers\Inventory\TransactionApprovalController;
use App\Http\Controllers\Users\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => auth()->check()
    ? redirect()->route('dashboard')
    : redirect()->route('login'))->name('home');

Route::middleware(['auth'])->group(function () {
    Route::get('dashboard', DashboardController::class)->name('dashboard');

    // Barang masuk
    Route::get('barang-masuk', [IncomingTransactionController::class, 'index'])
        ->name('inventory.incoming');
    Route::post('barang-masuk', [IncomingTransactionController::class, 'store'])
        ->name('inventory.incoming.store');
    Route::put('barang-masuk/{transaction}', [IncomingTransactionController::class, 'update'])
        ->name('inventory.incoming.update');
    Route::delete('barang-masuk/{transaction}', [IncomingTransactionController::class, 'destroy'])
        ->name('inventory.incoming.destroy');

    // Barang keluar
    Route::get('barang-keluar', [OutgoingTransactionController::class, 'index'])
        ->name('inventory.outgoing');
    Route::post('barang-keluar', [OutgoingTransactionController::class, 'store'])
        ->name('inventory.outgoing.store');
    Route::put('barang-keluar/{transaction}', [OutgoingTransactionController::class, 'update'])
        ->name('inventory.outgoing.update');
    Route::delete('barang-keluar/{transaction}', [OutgoingTransactionController::class, 'destroy'])
        ->name('inventory.outgoing.destroy');

    // Approval transaksi (Khusus Manager & SPV)
    Route::get('persetujuan', [TransactionApprovalController::class, 'index'])
        ->middleware('can:approve-transactions')
        ->name('inventory.approvals');
    Route::post('transaksi/{transaction}/setujui', [TransactionApprovalController::class, 'approve'])
        ->middleware('can:approve-transactions')
        ->name('inventory.transactions.approve');
    Route::post('transaksi/{transaction}/tolak', [TransactionApprovalController::class, 'reject'])
        ->middleware('can:approve-transactions')
        ->name('inventory.transactions.reject');

    // Stock / master barang
    Route::get('gudang', fn () => redirect()->route('inventory.stock'))->name('inventory.gudang');
    Route::get('stok', [ItemController::class, 'index'])->name('inventory.stock');
    Route::post('stok', [ItemController::class, 'store'])->name('inventory.stock.store');
    Route::put('stok/{item}', [ItemController::class, 'update'])->name('inventory.stock.update');
    Route::delete('stok/{item}', [ItemController::class, 'destroy'])->name('inventory.stock.destroy');

    // Laporan (Teknisi tidak memiliki akses)
    Route::get('laporan', [ReportController::class, 'index'])
        ->middleware('can:view-reports')
        ->name('inventory.reports');
    Route::get('laporan/export', [ReportController::class, 'export'])
        ->middleware('can:view-reports')
        ->name('inventory.reports.export');

    // Data master
    Route::resource('pelanggan', CustomerController::class)
        ->only(['index', 'store', 'update', 'destroy'])
        ->parameters(['pelanggan' => 'customer'])
        ->names('customers');

    Route::resource('bts-bsc', BtsBscController::class)
        ->only(['index', 'store', 'update', 'destroy'])
        ->parameters(['bts-bsc' => 'bts_bsc'])
        ->names('bts');

    // Pengelolaan pengguna (khusus SPV)
    Route::middleware('can:manage-users')->group(function () {
        Route::resource('pengguna', UserController::class)
            ->only(['index', 'store', 'update', 'destroy'])
            ->parameters(['pengguna' => 'user'])
            ->names('users');
    });
});

require __DIR__.'/settings.php';
