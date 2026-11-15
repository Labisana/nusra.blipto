<?php

namespace App\Http\Controllers\Inventory;

use App\Enums\ItemType;
use App\Http\Controllers\Controller;
use App\Http\Resources\CustomerResource;
use App\Models\Customer;
use App\Services\Inventory\ReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function index(Request $request, ReportService $reports): Response
    {
        $filters = $this->filters($request);

        return Inertia::render('inventory/Reports', [
            'incoming' => $reports->incoming($filters),
            'outgoing' => $reports->outgoing($filters),
            'stockRows' => $reports->stock($filters),
            'customers' => CustomerResource::collection(
                Customer::query()->orderBy('nama_pelanggan')->get(),
            ),
            'filters' => $filters,
            'itemTypes' => ItemType::options(),
        ]);
    }

    public function export(Request $request, ReportService $reports): StreamedResponse
    {
        $filters = $this->filters($request);
        $tab = $request->string('tab', 'masuk')->toString();

        [$rows, $headers] = match ($tab) {
            'keluar' => [$this->exportRows($reports->outgoing($filters)), [
                'Tanggal', 'Ref', 'Jenis Transaksi', 'Pelanggan', 'Tipe Pelanggan',
                'Barang', 'Jenis Barang', 'Qty', 'Serial', 'User',
            ]],
            'stok' => [$this->stockExportRows($reports->stock($filters)), [
                'SKU', 'Barang', 'Jenis', 'Total Masuk', 'Total Keluar', 'Sisa Stok', 'Satuan', 'Lokasi',
            ]],
            default => [$this->exportRows($reports->incoming($filters)), [
                'Tanggal', 'Ref', 'Jenis Transaksi', 'Pelanggan', 'Tipe Pelanggan',
                'Barang', 'Jenis Barang', 'Qty', 'Serial', 'User',
            ]],
        };

        $filename = "laporan-{$tab}-".now()->format('Ymd-His').'.csv';

        return response()->streamDownload(function () use ($rows, $headers): void {
            $handle = fopen('php://output', 'w');

            if ($handle === false) {
                return;
            }

            fputcsv($handle, $headers);

            foreach ($rows as $row) {
                fputcsv($handle, $row);
            }

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    /**
     * @return array{from: string|null, to: string|null, item_type: string|null, customer_id: int|null}
     */
    protected function filters(Request $request): array
    {
        $validated = $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
            'item_type' => ['nullable', 'string', 'in:serial,non-serial'],
            'customer_id' => ['nullable', 'integer', 'exists:customers,id'],
        ]);

        return [
            'from' => $validated['from'] ?? null,
            'to' => $validated['to'] ?? null,
            'item_type' => $validated['item_type'] ?? null,
            'customer_id' => isset($validated['customer_id']) ? (int) $validated['customer_id'] : null,
        ];
    }

    /**
     * @param  Collection<int, array<string, mixed>>  $rows
     * @return array<int, array<int, mixed>>
     */
    protected function exportRows(Collection $rows): array
    {
        return $rows->map(fn (array $row): array => [
            $row['date'],
            $row['ref'],
            $row['activity'],
            $row['customer'],
            $row['tipe_pelanggan'],
            $row['item'],
            $row['type'],
            $row['qty'],
            $row['serials'],
            $row['user'],
        ])->all();
    }

    /**
     * @param  Collection<int, array<string, mixed>>  $rows
     * @return array<int, array<int, mixed>>
     */
    protected function stockExportRows(Collection $rows): array
    {
        return $rows->map(fn (array $row): array => [
            $row['sku'],
            $row['item'],
            $row['type_label'],
            $row['in'],
            $row['out'],
            $row['remaining'],
            $row['unit'],
            $row['location'],
        ])->all();
    }
}
