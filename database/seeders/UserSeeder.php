<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            ['name' => 'Manager Operasional', 'email' => 'manager@nusra.test', 'role' => Role::Manager],
            ['name' => 'Supervisor Gudang', 'email' => 'spv@nusra.test', 'role' => Role::Spv],
            ['name' => 'Admin Gudang', 'email' => 'admin@nusra.test', 'role' => Role::Admin],
            ['name' => 'Teknisi Lapangan', 'email' => 'teknisi@nusra.test', 'role' => Role::Teknisi],
        ];

        foreach ($users as $user) {
            User::query()->updateOrCreate(
                ['email' => $user['email']],
                [
                    'name' => $user['name'],
                    'role' => $user['role'],
                    'email_verified_at' => now(),
                    'password' => 'password',
                ],
            );
        }
    }
}
