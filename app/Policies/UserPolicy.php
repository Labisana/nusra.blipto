<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Hanya SPV yang boleh mengelola akun pengguna.
     */
    public function viewAny(User $user): bool
    {
        return $user->isSpv();
    }

    public function view(User $user, User $target): bool
    {
        return $user->isSpv();
    }

    public function create(User $user): bool
    {
        return $user->isSpv();
    }

    public function update(User $user, User $target): bool
    {
        return $user->isSpv();
    }

    /**
     * Akun sendiri tidak boleh dihapus agar tidak kehilangan akses.
     */
    public function delete(User $user, User $target): bool
    {
        return $user->isSpv() && $user->id !== $target->id;
    }
}
