<?php

namespace App\Http\Controllers\Inventory;

use App\Enums\TransactionActivity;
use App\Enums\TransactionDirection;
use App\Http\Controllers\Controller;
use App\Http\Requests\Inventory\StoreIncomingTransactionRequest;
use App\Http\Requests\Inventory\UpdateIncomingTransactionRequest;
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

class IncomingTransactionController extends Controller
{
    public function index(Request $request): Response
    {
        $transactions = StockTransaction::query()
            ->direction(TransactionDirection::In)
            ->with(['customer', 'user', 'items.item', 'items.serialNumbers'])
            ->orderByDesc('transaction_date')
            ->orderByDesc('id')
            ->limit(100)
            ->get();

        return Inertia::render('inventory/Incoming', [
            'transactions' => StockTransactionResource::collection($transactions),
            'customers' => CustomerResource::collection(
                Customer::query()->orderBy('nama_pelanggan')->get(),
            ),
            'items' => ItemResource::collection(
                Item::query()->withStockTotals()->orderBy('name')->get(),
            ),
            'activities' => TransactionActivity::optionsFor(TransactionDirection::In),
        ]);
    }

    public function store(
        StoreIncomingTransactionRequest $request,
        StockTransactionService $service,
    ): RedirectResponse {
        $transaction = $service->create(
            TransactionDirection::In,
            $request->validated(),
            $request->user(),
        );

        return to_route('inventory.incoming')->with('toast', [
            'type' => 'success',
            'message' => "Barang masuk {$transaction->reference} berhasil disimpan.",
        ]);
    }

    public function update(
        UpdateIncomingTransactionRequest $request,
        StockTransaction $transaction,
        StockTransactionService $service,
    ): RedirectResponse {
        abort_unless($transaction->direction === TransactionDirection::In, 404);

        $service->update($transaction, $request->validated());

        return to_route('inventory.incoming')->with('toast', [
            'type' => 'success',
            'message' => "Barang masuk {$transaction->reference} berhasil diperbarui.",
        ]);
    }

    public function destroy(
        StockTransaction $transaction,
        StockTransactionService $service,
    ): RedirectResponse {
        abort_unless($transaction->direction === TransactionDirection::In, 404);

        $this->authorize('delete', $transaction);

        $service->delete($transaction);

        return to_route('inventory.incoming')->with('toast', [
            'type' => 'success',
            'message' => 'Transaksi barang masuk berhasil dihapus.',
        ]);
    }
}
