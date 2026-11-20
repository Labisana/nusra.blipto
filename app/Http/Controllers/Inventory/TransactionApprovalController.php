<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\StockTransaction;
use App\Services\Inventory\StockTransactionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class TransactionApprovalController extends Controller
{
    public function approve(
        StockTransaction $transaction,
        StockTransactionService $service,
        Request $request,
    ): RedirectResponse {
        abort_unless($request->user()->canManage(), 403, 'Hanya SPV/Admin yang dapat menyetujui permintaan.');

        $service->approve($transaction, $request->user());

        return back()->with('toast', [
            'type' => 'success',
            'message' => "Permintaan transaksi {$transaction->reference} berhasil disetujui.",
        ]);
    }

    public function reject(
        StockTransaction $transaction,
        StockTransactionService $service,
        Request $request,
    ): RedirectResponse {
        abort_unless($request->user()->canManage(), 403, 'Hanya SPV/Admin yang dapat menolak permintaan.');

        $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);

        $service->reject($transaction, $request->user(), $request->input('reason'));

        return back()->with('toast', [
            'type' => 'warning',
            'message' => "Permintaan transaksi {$transaction->reference} telah ditolak.",
        ]);
    }
}
