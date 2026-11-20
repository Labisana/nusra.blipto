<?php

namespace App\Http\Controllers\Inventory;

use App\Enums\TransactionDirection;
use App\Enums\TransactionStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\StockTransactionResource;
use App\Models\StockTransaction;
use App\Services\Inventory\StockTransactionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TransactionApprovalController extends Controller
{
    public function index(Request $request): Response
    {
        abort_unless($request->user()->canApprove(), 403, 'Hanya Manager dan SPV yang dapat mengakses halaman approval.');

        $pending = StockTransaction::query()
            ->pending()
            ->with(['customer', 'user', 'approvedBy', 'items.item', 'items.serialNumbers'])
            ->orderByDesc('id')
            ->get();

        $history = StockTransaction::query()
            ->whereIn('status', [TransactionStatus::Approved, TransactionStatus::Rejected])
            ->whereNotNull('approved_at')
            ->with(['customer', 'user', 'approvedBy', 'items.item', 'items.serialNumbers'])
            ->orderByDesc('approved_at')
            ->limit(50)
            ->get();

        $stats = [
            'pending_total' => $pending->count(),
            'pending_in' => $pending->where('direction', TransactionDirection::In)->count(),
            'pending_out' => $pending->where('direction', TransactionDirection::Out)->count(),
            'approved_today' => StockTransaction::query()
                ->where('status', TransactionStatus::Approved)
                ->whereDate('approved_at', today())
                ->count(),
            'rejected_today' => StockTransaction::query()
                ->where('status', TransactionStatus::Rejected)
                ->whereDate('approved_at', today())
                ->count(),
        ];

        return Inertia::render('inventory/Approvals', [
            'pending' => StockTransactionResource::collection($pending),
            'history' => StockTransactionResource::collection($history),
            'stats' => $stats,
        ]);
    }

    public function approve(
        StockTransaction $transaction,
        StockTransactionService $service,
        Request $request,
    ): RedirectResponse {
        abort_unless($request->user()->canApprove(), 403, 'Hanya Manager dan SPV yang dapat menyetujui permintaan.');

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
        abort_unless($request->user()->canApprove(), 403, 'Hanya Manager dan SPV yang dapat menolak permintaan.');

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
