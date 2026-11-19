<?php

namespace App\Policies;

use App\Models\StockTransaction;
use App\Models\User;

class StockTransactionPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, StockTransaction $transaction): bool
    {
        return true;
    }

    /**
     * Seluruh peran (termasuk Teknisi) boleh membuat transaksi masuk/keluar.
     */
    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, StockTransaction $transaction): bool
    {
        return $user->canManage();
    }

    public function delete(User $user, StockTransaction $transaction): bool
    {
        return $user->canManage();
    }
}
