<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use App\Services\PpidAdminService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(PpidAdminService $admins): View
    {
        $users = User::query()->orderBy('name')->paginate(20);

        return view('admin.users.index', compact('users', 'admins'));
    }

    public function create(): View
    {
        return view('admin.users.create', [
            'user' => new User(['role' => UserRole::Editor, 'is_active' => true]),
            'roles' => UserRole::cases(),
        ]);
    }

    public function store(Request $request, PpidAdminService $admins): RedirectResponse
    {
        $data = $this->validated($request);

        if (($data['role'] ?? null) === UserRole::AdminPpid->value && ! $admins->canManagePpidAdmins($request->user())) {
            abort(403, 'Hanya Admin PPID inti yang dapat membuat akun Admin PPID.');
        }

        $data['password'] = Hash::make($data['password']);

        $user = User::query()->create($data);
        ActivityLog::record('created', $user, 'Membuat pengguna: '.$user->email);

        return redirect()->route('admin.users.index')->with('status', 'Pengguna berhasil dibuat.');
    }

    public function edit(User $user, PpidAdminService $admins): View
    {
        if ($admins->isProtectedAdmin($user)) {
            abort(403, 'Akun Admin PPID inti tidak dapat diubah melalui manajemen pengguna.');
        }

        return view('admin.users.edit', [
            'user' => $user,
            'roles' => UserRole::cases(),
        ]);
    }

    public function update(Request $request, User $user, PpidAdminService $admins): RedirectResponse
    {
        $this->authorize('update', $user);

        if ($admins->isProtectedAdmin($user)) {
            abort(403, 'Akun Admin PPID inti tidak dapat diubah melalui manajemen pengguna.');
        }

        $data = $this->validated($request, $user);

        if ($admins->isProtectedAdmin($user) || ($user->hasRole(UserRole::AdminPpid) && $admins->isProtectedIdentity($user->email, $user->username))) {
            unset($data['role'], $data['is_active']);
        }

        if (($data['role'] ?? null) !== UserRole::AdminPpid->value && $user->hasRole(UserRole::AdminPpid) && $admins->isProtectedAdmin($user)) {
            abort(403);
        }

        if (! empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);
        ActivityLog::record('updated', $user, 'Memperbarui pengguna: '.$user->email);

        return redirect()->route('admin.users.index')->with('status', 'Pengguna berhasil diperbarui.');
    }

    public function destroy(Request $request, User $user, PpidAdminService $admins): RedirectResponse
    {
        $this->authorize('delete', $user);

        if ($admins->isProtectedAdmin($user)) {
            abort(403, 'Akun Admin PPID inti tidak dapat dihapus.');
        }

        if ($user->id === $request->user()->id) {
            return back()->withErrors(['user' => 'Tidak dapat menghapus akun Anda sendiri.']);
        }

        ActivityLog::record('deleted', $user, 'Menghapus pengguna: '.$user->email);
        $user->delete();

        return redirect()->route('admin.users.index')->with('status', 'Pengguna berhasil dihapus.');
    }

    /** @return array<string, mixed> */
    private function validated(Request $request, ?User $user = null): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user?->id)],
            'username' => ['nullable', 'string', 'max:64', 'alpha_dash', Rule::unique('users', 'username')->ignore($user?->id)],
            'role' => ['required', Rule::in(UserRole::values())],
            'is_active' => ['sometimes', 'boolean'],
            'password' => [$user ? 'nullable' : 'required', 'confirmed', Password::defaults()],
        ]);

        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }
}
