<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserDetail;
use App\Models\masterData\Departemen;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function index()
    {
        $this->authorize('view users');
        $users = User::with(['roles', 'detail.departemen'])->latest()->get();
        return view('users.index', compact('users'));
    }

    public function create()
    {
        $this->authorize('create users');
        $roles = Role::all();
        $departements = Departemen::where('is_active', true)->orderBy('name')->get();
        return view('users.create', compact('roles', 'departements'));
    }

    public function store(Request $request)
    {
        $this->authorize('create users');

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'roles' => 'nullable|array',
            'id_departemen' => 'nullable|exists:departemens,id',
            'position' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'gender' => 'nullable|in:L,P',
            'description' => 'nullable|string',
        ]);

        DB::transaction(function () use ($validated) {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
            ]);

            if (isset($validated['roles'])) {
                $user->syncRoles($validated['roles']);
            }

            UserDetail::create([
                'id_user' => $user->id,
                'id_departemen' => $validated['id_departemen'] ?? null,
                'position' => $validated['position'] ?? null,
                'phone' => $validated['phone'] ?? null,
                'address' => $validated['address'] ?? null,
                'gender' => $validated['gender'] ?? null,
                'description' => $validated['description'] ?? null,
            ]);
        });

        return redirect()->route('users.index')->with('success', 'User berhasil dibuat.');
    }

    public function edit(User $user)
    {
        $this->authorize('edit users');
        $user->load('detail');
        $roles = Role::all();
        $departements = Departemen::where('is_active', true)->orderBy('name')->get();
        return view('users.edit', compact('user', 'roles', 'departements'));
    }

    public function update(Request $request, User $user)
    {
        $this->authorize('edit users');

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'roles' => 'nullable|array',
            'id_departemen' => 'nullable|exists:departemens,id',
            'position' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'gender' => 'nullable|in:L,P',
            'description' => 'nullable|string',
        ]);

        DB::transaction(function () use ($user, $validated) {
            $user->update([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => !empty($validated['password']) ? Hash::make($validated['password']) : $user->password,
            ]);

            if (isset($validated['roles'])) {
                $user->syncRoles($validated['roles']);
            }

            $user->detail()->updateOrCreate(
                ['id_user' => $user->id],
                [
                    'id_departemen' => $validated['id_departemen'] ?? null,
                    'position' => $validated['position'] ?? null,
                    'phone' => $validated['phone'] ?? null,
                    'address' => $validated['address'] ?? null,
                    'gender' => $validated['gender'] ?? null,
                    'description' => $validated['description'] ?? null,
                ]
            );
        });

        return redirect()->route('users.index')->with('success', 'User berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        $this->authorize('delete users');

        DB::transaction(function () use ($user) {
            $user->detail()->delete();
            $user->delete();
        });

        return redirect()->route('users.index')->with('success', 'User berhasil dihapus.');
    }
}
