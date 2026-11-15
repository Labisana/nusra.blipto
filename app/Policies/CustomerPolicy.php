<?php

namespace App\Policies;

use App\Models\Customer;
use App\Models\User;

class CustomerPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Customer $customer): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->canManage();
    }

    public function update(User $user, Customer $customer): bool
    {
        return $user->canManage();
    }

    public function delete(User $user, Customer $customer): bool
    {
        return $user->canManage() && ! $customer->stockTransactions()->exists();
    }
}
