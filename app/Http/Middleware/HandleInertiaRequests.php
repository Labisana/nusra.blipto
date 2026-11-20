<?php

namespace App\Http\Middleware;

use App\Models\StockTransaction;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $user = $request->user();

        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'auth' => [
                'user' => $user ? [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'email_verified_at' => $user->email_verified_at?->toIso8601String(),
                    'role' => $user->role->value,
                    'can_manage' => $user->canManage(),
                    'can_view_reports' => $user->canViewReports(),
                    'can_manage_users' => $user->canManageUsers(),
                    'can_approve' => $user->canApprove(),
                ] : null,
            ],
            'flash' => [
                'toast' => fn (): ?array => $request->session()->get('toast'),
                'whatsapp_report' => fn (): ?array => $request->session()->get('whatsapp_report'),
            ],
            'pending_approvals_count' => fn (): int => $user && $user->canApprove()
                ? StockTransaction::query()->pending()->count()
                : 0,
            'pending_approvals' => fn (): array => $user && $user->canApprove()
                ? StockTransaction::query()
                    ->pending()
                    ->with(['user:id,name', 'customer:id,nama_pelanggan', 'items.item'])
                    ->orderByDesc('id')
                    ->limit(8)
                    ->get()
                    ->map(fn ($t) => [
                        'id' => $t->id,
                        'reference' => $t->reference,
                        'direction' => $t->direction->value,
                        'direction_label' => $t->direction->label(),
                        'activity' => $t->activity->value,
                        'date' => $t->transaction_date->format('d/m/Y'),
                        'created_at_human' => $t->created_at?->diffForHumans() ?? '',
                        'user_name' => $t->user?->name ?? 'Teknisi',
                        'customer_name' => $t->customer?->nama_pelanggan,
                        'items_count' => $t->items->count(),
                        'summary' => $t->items->map(fn ($i) => "{$i->quantity}x {$i->item->name}")->join(', '),
                    ])
                    ->toArray()
                : [],
            'sidebarOpen' => ! $request->hasCookie('sidebar_state') || $request->cookie('sidebar_state') === 'true',
        ];
    }
}
