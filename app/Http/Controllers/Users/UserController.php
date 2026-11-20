<?php

namespace App\Http\Controllers\Users;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Http\Requests\Users\StoreUserRequest;
use App\Http\Requests\Users\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    public function index(Request $request): Response
    {
        $this->authorize('viewAny', User::class);

        $search = $request->string('q')->toString();

        $users = User::query()
            ->search($search)
            ->orderBy('name')
            ->get();

        return Inertia::render('users/Index', [
            'users' => UserResource::collection($users),
            'roles' => Role::options(),
            'filters' => ['q' => $search],
        ]);
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $user = User::query()->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'role' => $data['role'],
            'password' => $data['password'],
        ]);

        $user->markEmailAsVerified();

        return back()->with('toast', [
            'type' => 'success',
            'message' => "Pengguna {$user->name} berhasil ditambahkan.",
        ]);
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $data = $request->validated();

        if ($user->is($request->user()) && $data['role'] !== $user->role->value) {
            return back()->with('toast', [
                'type' => 'error',
                'message' => 'Anda tidak dapat mengubah peran akun sendiri.',
            ]);
        }

        $user->fill([
            'name' => $data['name'],
            'email' => $data['email'],
            'role' => $data['role'],
        ]);

        if (filled($data['password'] ?? null)) {
            $user->password = $data['password'];
        }

        $user->save();

        return back()->with('toast', [
            'type' => 'success',
            'message' => "Pengguna {$user->name} berhasil diperbarui.",
        ]);
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if (! $request->user()->can('delete', $user)) {
            return back()->with('toast', [
                'type' => 'error',
                'message' => $request->user()->canManageUsers()
                    ? 'Anda tidak dapat menghapus akun sendiri.'
                    : 'Anda tidak memiliki akses untuk menghapus pengguna.',
            ]);
        }

        $user->delete();

        return back()->with('toast', [
            'type' => 'success',
            'message' => "Pengguna {$user->name} berhasil dihapus.",
        ]);
    }
}
