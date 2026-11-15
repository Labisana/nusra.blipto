<?php

namespace App\Http\Controllers\Inventory;

use App\Enums\CustomerStatus;
use App\Enums\CustomerType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Customers\StoreCustomerRequest;
use App\Http\Requests\Customers\UpdateCustomerRequest;
use App\Http\Resources\BtsBscResource;
use App\Http\Resources\CustomerResource;
use App\Models\BtsBsc;
use App\Models\Customer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CustomerController extends Controller
{
    public function index(Request $request): Response
    {
        $search = $request->string('q')->toString();

        $customers = Customer::query()
            ->with('btsBsc')
            ->search($search)
            ->orderBy('nama_pelanggan')
            ->get();

        return Inertia::render('customers/Index', [
            'customers' => CustomerResource::collection($customers),
            'btsOptions' => BtsBscResource::collection(
                BtsBsc::query()->orderBy('nama')->get(),
            ),
            'filters' => ['q' => $search],
            'options' => [
                'statuses' => CustomerStatus::values(),
                'types' => array_map(fn (CustomerType $type): string => $type->value, CustomerType::cases()),
            ],
        ]);
    }

    public function store(StoreCustomerRequest $request): RedirectResponse
    {
        $customer = Customer::query()->create($request->validated());

        return back()->with('toast', [
            'type' => 'success',
            'message' => "Pelanggan {$customer->nama_pelanggan} berhasil ditambahkan.",
        ]);
    }

    public function update(UpdateCustomerRequest $request, Customer $customer): RedirectResponse
    {
        $customer->update($request->validated());

        return back()->with('toast', [
            'type' => 'success',
            'message' => "Pelanggan {$customer->nama_pelanggan} berhasil diperbarui.",
        ]);
    }

    public function destroy(Request $request, Customer $customer): RedirectResponse
    {
        if (! $request->user()->can('delete', $customer)) {
            return back()->with('toast', [
                'type' => 'error',
                'message' => $request->user()->canManage()
                    ? 'Pelanggan tidak dapat dihapus karena sudah memiliki transaksi.'
                    : 'Anda tidak memiliki akses untuk menghapus pelanggan.',
            ]);
        }

        $customer->delete();

        return back()->with('toast', [
            'type' => 'success',
            'message' => "Pelanggan {$customer->nama_pelanggan} berhasil dihapus.",
        ]);
    }
}
