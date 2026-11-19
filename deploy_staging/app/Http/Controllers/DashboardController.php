<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Services\Inventory\ReportService;
use App\Services\Inventory\StockSummaryService;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(StockSummaryService $summary, ReportService $reports): Response
    {
        $lowStock = $summary->lowStock(5)
            ->map(fn (Item $item): array => [
                'item' => $item->name,
                'stock' => $item->remaining,
                'min' => $item->min_stock,
                'type' => $item->type->label(),
            ])
            ->values();

        return Inertia::render('Dashboard', [
            'stats' => $summary->dashboardStats(),
            'recentIncoming' => $reports->incoming([], 4),
            'recentOutgoing' => $reports->outgoing([], 4),
            'lowStock' => $lowStock,
        ]);
    }
}
