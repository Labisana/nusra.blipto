<?php

namespace App\Http\Controllers\Inventory;

use App\Enums\BtsStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Bts\StoreBtsBscRequest;
use App\Http\Requests\Bts\UpdateBtsBscRequest;
use App\Http\Resources\BtsBscResource;
use App\Models\BtsBsc;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class BtsBscController extends Controller
{
    public function index(Request $request): Response
    {
        $search = $request->string('q')->toString();

        return Inertia::render('bts/Index', [
            'btsList' => BtsBscResource::collection(
                BtsBsc::query()->search($search)->orderBy('nama')->get(),
            ),
            'filters' => ['q' => $search],
            'statuses' => BtsStatus::values(),
        ]);
    }

    public function store(StoreBtsBscRequest $request): RedirectResponse
    {
        $btsBsc = BtsBsc::query()->create($request->validated());

        return back()->with('toast', [
            'type' => 'success',
            'message' => "Data {$btsBsc->nama} berhasil ditambahkan.",
        ]);
    }

    public function update(UpdateBtsBscRequest $request, BtsBsc $btsBsc): RedirectResponse
    {
        $btsBsc->update($request->validated());

        return back()->with('toast', [
            'type' => 'success',
            'message' => "Data {$btsBsc->nama} berhasil diperbarui.",
        ]);
    }

    public function destroy(Request $request, BtsBsc $btsBsc): RedirectResponse
    {
        if (! $request->user()->can('delete', $btsBsc)) {
            return back()->with('toast', [
                'type' => 'error',
                'message' => $request->user()->canManage()
                    ? 'Data BTS/BSC tidak dapat dihapus karena masih digunakan oleh pelanggan.'
                    : 'Anda tidak memiliki akses untuk menghapus data BTS/BSC.',
            ]);
        }

        $btsBsc->delete();

        return back()->with('toast', [
            'type' => 'success',
            'message' => "Data {$btsBsc->nama} berhasil dihapus.",
        ]);
    }
}
