<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

test('guests are redirected to login from user management', function () {
    $this->get(route('users.index'))->assertRedirect(route('login'));
});

test('spv can see the user management page with users and roles', function () {
    $spv = User::factory()->spv()->create();
    User::factory()->admin()->create();

    $this->actingAs($spv)
        ->get(route('users.index'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('users/Index')
            ->has('users', 2)
            ->has('roles', 4)
            ->where('roles.0.value', 'manager')
            ->where('roles.1.value', 'spv')
            ->where('roles.3.value', 'teknisi')
            ->where('filters.q', ''));
});

test('manager can also see and manage users', function () {
    $manager = User::factory()->manager()->create();
    User::factory()->admin()->create();

    $this->actingAs($manager)
        ->get(route('users.index'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('users/Index')
            ->has('users', 2)
            ->has('roles', 4));
});

test('admin and teknisi cannot access user management', function () {
    $admin = User::factory()->admin()->create();
    $teknisi = User::factory()->teknisi()->create();

    foreach ([$admin, $teknisi] as $user) {
        $this->actingAs($user)
            ->get(route('users.index'))
            ->assertForbidden();

        $this->actingAs($user)
            ->post(route('users.store'), [
                'name' => 'Tidak Boleh',
                'email' => 'tidak-boleh@nusra.test',
                'role' => 'teknisi',
                'password' => 'password123',
            ])
            ->assertForbidden();
    }

    expect(User::query()->where('email', 'tidak-boleh@nusra.test')->exists())->toBeFalse();
});

test('spv can create a user with a hashed password and lowercase email', function () {
    $spv = User::factory()->spv()->create();

    $this->actingAs($spv)
        ->post(route('users.store'), [
            'name' => 'Teknisi Baru',
            'email' => 'Teknisi.Baru@Nusra.Test',
            'role' => 'teknisi',
            'password' => 'rahasia123',
        ])
        ->assertRedirect()
        ->assertSessionHas('toast');

    $user = User::query()->where('email', 'teknisi.baru@nusra.test')->sole();

    expect($user->name)->toBe('Teknisi Baru')
        ->and($user->role->value)->toBe('teknisi')
        ->and($user->email_verified_at)->not->toBeNull()
        ->and(Hash::check('rahasia123', $user->password))->toBeTrue()
        ->and($user->password)->not->toBe('rahasia123');
});

test('spv can update a user without changing the password', function () {
    $spv = User::factory()->spv()->create();
    $target = User::factory()->admin()->create(['password' => 'password-lama']);
    $oldHash = $target->password;

    $this->actingAs($spv)
        ->put(route('users.update', $target), [
            'name' => 'Admin Diperbarui',
            'email' => $target->email,
            'role' => 'admin',
            'password' => '',
        ])
        ->assertRedirect()
        ->assertSessionHas('toast');

    $target->refresh();

    expect($target->name)->toBe('Admin Diperbarui')
        ->and($target->password)->toBe($oldHash)
        ->and(Hash::check('password-lama', $target->password))->toBeTrue();
});

test('spv can update a user with a new password', function () {
    $spv = User::factory()->spv()->create();
    $target = User::factory()->teknisi()->create();

    $this->actingAs($spv)
        ->put(route('users.update', $target), [
            'name' => $target->name,
            'email' => $target->email,
            'role' => 'admin',
            'password' => 'password-baru',
        ])
        ->assertRedirect();

    $target->refresh();

    expect($target->role->value)->toBe('admin')
        ->and(Hash::check('password-baru', $target->password))->toBeTrue();
});

test('spv cannot change their own role', function () {
    $spv = User::factory()->spv()->create();

    $this->actingAs($spv)
        ->put(route('users.update', $spv), [
            'name' => $spv->name,
            'email' => $spv->email,
            'role' => 'admin',
            'password' => '',
        ])
        ->assertRedirect()
        ->assertSessionHas('toast', fn (array $toast): bool => $toast['type'] === 'error');

    expect($spv->fresh()->role->value)->toBe('spv');
});

test('spv cannot delete their own account', function () {
    $spv = User::factory()->spv()->create();

    $this->actingAs($spv)
        ->delete(route('users.destroy', $spv))
        ->assertRedirect()
        ->assertSessionHas('toast', fn (array $toast): bool => $toast['type'] === 'error');

    expect(User::query()->whereKey($spv->id)->exists())->toBeTrue();
});

test('spv can delete another user', function () {
    $spv = User::factory()->spv()->create();
    $target = User::factory()->teknisi()->create();

    $this->actingAs($spv)
        ->delete(route('users.destroy', $target))
        ->assertRedirect()
        ->assertSessionHas('toast', fn (array $toast): bool => $toast['type'] === 'success');

    expect(User::query()->whereKey($target->id)->exists())->toBeFalse();
});

test('user management validates input', function () {
    $spv = User::factory()->spv()->create();
    User::factory()->create(['email' => 'sudah-ada@nusra.test']);

    $this->actingAs($spv)
        ->post(route('users.store'), [
            'name' => 'Duplikat',
            'email' => 'sudah-ada@nusra.test',
            'role' => 'teknisi',
            'password' => 'password123',
        ])
        ->assertSessionHasErrors('email');

    $this->actingAs($spv)
        ->post(route('users.store'), [
            'name' => 'Peran Salah',
            'email' => 'peran-salah@nusra.test',
            'role' => 'superadmin',
            'password' => 'password123',
        ])
        ->assertSessionHasErrors('role');

    $this->actingAs($spv)
        ->post(route('users.store'), [
            'name' => 'Password Pendek',
            'email' => 'password-pendek@nusra.test',
            'role' => 'teknisi',
            'password' => 'pendek',
        ])
        ->assertSessionHasErrors('password');

    expect(User::query()->where('email', 'peran-salah@nusra.test')->exists())->toBeFalse()
        ->and(User::query()->where('email', 'password-pendek@nusra.test')->exists())->toBeFalse();
});
