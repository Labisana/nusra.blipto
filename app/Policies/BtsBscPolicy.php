<?php

namespace App\Policies;

use App\Models\BtsBsc;
use App\Models\User;

class BtsBscPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, BtsBsc $btsBsc): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->canManage();
    }

    public function update(User $user, BtsBsc $btsBsc): bool
    {
        return $user->canManage();
    }

    public function delete(User $user, BtsBsc $btsBsc): bool
    {
        return $user->canManage() && ! $btsBsc->customers()->exists();
    }
}
