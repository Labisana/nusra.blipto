<?php

namespace App\Http\Controllers\Inventory;

use App\Enums\ItemType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Inventory\StoreItemRequest;
use App\Http\Requests\Inventory\UpdateItemRequest;
use App\Http\Resources\ItemResource;
use App\Http\Resources\SerialNumberResource;
use App\Models\Item;
use App\Models\SerialNumber;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ItemController extends Controller
{
    public function index(Request $request): Response
    {
        $items = Item::query()
            ->withStockTotals()
            ->with(['serialNumbers.item', 'serialNumbers.customer'])
            ->search($request->string('q')->toString())
            ->ofType($request->string('type')->toString())
            ->orderBy('name')
            ->get();

        $serials = SerialNumber::query()
            ->with(['item', 'customer'])
            ->orderBy('serial_number')
            ->get();

        return Inertia::render('inventory/Stock', [
            'items' => ItemResource::collection($items),
            'serials' => SerialNumberResource::collection($serials),
            'itemTypes' => ItemType::options(),
            'filters' => [
                'q' => $request->string('q')->toString(),
                'type' => $request->string('type')->toString(),
            ],
        ]);
    }

    public function store(StoreItemRequest $request): RedirectResponse
    {
        $item = Item::query()->create([
            ...$request->validated(),
            'min_stock' => $request->integer('min_stock'),
        ]);

        return back()->with('toast', [
            'type' => 'success',
            'message' => "Barang {$item->name} berhasil ditambahkan.",
        ]);
    }

    public function update(UpdateItemRequest $request, Item $item): RedirectResponse
    {
        $item->update([
            ...$request->validated(),
            'min_stock' => $request->integer('min_stock'),
        ]);

        return back()->with('toast', [
            'type' => 'success',
            'message' => "Barang {$item->name} berhasil diperbarui.",
        ]);
    }

    public function destroy(Request $request, Item $item): RedirectResponse
    {
        if (! $request->user()->can('delete', $item)) {
            return back()->with('toast', [
                'type' => 'error',
                'message' => $request->user()->canManage()
                    ? 'Barang tidak dapat dihapus karena sudah digunakan pada transaksi.'
                    : 'Anda tidak memiliki akses untuk menghapus barang.',
            ]);
        }

        $item->delete();

        return back()->with('toast', [
            'type' => 'success',
            'message' => "Barang {$item->name} berhasil dihapus.",
        ]);
    }
}
