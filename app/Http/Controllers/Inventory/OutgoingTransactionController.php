<?php

namespace App\Http\Controllers\Inventory;

use App\Enums\TransactionActivity;
use App\Enums\TransactionDirection;
use App\Http\Controllers\Controller;
use App\Http\Requests\Inventory\StoreOutgoingTransactionRequest;
use App\Http\Requests\Inventory\UpdateOutgoingTransactionRequest;
use App\Http\Resources\CustomerResource;
use App\Http\Resources\ItemResource;
use App\Http\Resources\StockTransactionResource;
use App\Models\Customer;
use App\Models\Item;
use App\Models\StockTransaction;
use App\Services\Inventory\StockTransactionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class OutgoingTransactionController extends Controller
{
    public function index(Request $request): Response
    {
        $transactions = StockTransaction::query()
            ->direction(TransactionDirection::Out)
            ->with(['customer', 'user', 'approvedBy', 'items.item', 'items.serialNumbers'])
            ->orderByDesc('transaction_date')
            ->orderByDesc('id')
            ->limit(100)
            ->get();

        $items = Item::query()
            ->withStockTotals()
            ->with(['availableSerialNumbers' => fn ($query) => $query->orderBy('serial_number')])
            ->orderBy('name')
            ->get();

        return Inertia::render('inventory/Outgoing', [
            'transactions' => StockTransactionResource::collection($transactions),
            'customers' => CustomerResource::collection(
                Customer::query()->orderBy('nama_pelanggan')->get(),
            ),
            'items' => ItemResource::collection($items),
            'activities' => TransactionActivity::optionsFor(TransactionDirection::Out),
        ]);
    }

    public function store(
        StoreOutgoingTransactionRequest $request,
        StockTransactionService $service,
    ): RedirectResponse {
        $transaction = $service->create(
            TransactionDirection::Out,
            $request->validated(),
            $request->user(),
        );

        $isPending = $transaction->isPending();
        $message = $isPending
            ? "Permintaan barang keluar {$transaction->reference} berhasil diajukan dan menunggu approval."
            : "Barang keluar {$transaction->reference} berhasil disimpan.";

        return to_route('inventory.outgoing')
            ->with('toast', [
                'type' => 'success',
                'message' => $message,
            ])
            ->with('whatsapp_report', $transaction->toWhatsAppReport());
    }

    public function update(
        UpdateOutgoingTransactionRequest $request,
        StockTransaction $transaction,
        StockTransactionService $service,
    ): RedirectResponse {
        abort_unless($transaction->direction === TransactionDirection::Out, 404);

        $service->update($transaction, $request->validated());

        return to_route('inventory.outgoing')->with('toast', [
            'type' => 'success',
            'message' => "Barang keluar {$transaction->reference} berhasil diperbarui.",
        ]);
    }

    public function destroy(
        StockTransaction $transaction,
        StockTransactionService $service,
    ): RedirectResponse {
        abort_unless($transaction->direction === TransactionDirection::Out, 404);

        $this->authorize('delete', $transaction);

        $service->delete($transaction);

        return to_route('inventory.outgoing')->with('toast', [
            'type' => 'success',
            'message' => 'Transaksi barang keluar berhasil dihapus.',
        ]);
    }
}
