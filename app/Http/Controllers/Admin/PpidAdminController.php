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

class PpidAdminController extends Controller
{
    public function __construct(private PpidAdminService $admins) {}

    public function index(): View
    {
        $this->authorize('viewAdminManagement', User::class);
        $users = User::query()
            ->where('role', UserRole::AdminPpid)
            ->orderBy('name')
            ->get();

        return view('admin.ppid.admins.index', [
            'users' => $users,
            'admins' => $this->admins,
        ]);
    }

    public function create(): View
    {
        $this->authorize('createAdmin', User::class);

        return view('admin.ppid.admins.form', [
            'user' => new User(['is_active' => true, 'role' => UserRole::AdminPpid]),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('createAdmin', User::class);

        $data = $this->validated($request);
        $password = $data['password'];
        unset($data['password'], $data['password_confirmation']);

        $user = User::query()->create([
            'name' => $data['name'],
            'email' => strtolower($data['email']),
            'username' => $data['username'],
            'password' => Hash::make($password),
            'role' => UserRole::AdminPpid,
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        ActivityLog::record('admin_created', $user, 'Membuat Admin PPID: '.$user->email, [
            'username' => $user->username,
        ]);

        return redirect()
            ->route('admin.ppid.admins.index')
            ->with('status', 'Admin PPID tambahan berhasil dibuat.');
    }

    public function edit(User $user): View
    {
        $this->assertPpidAdmin($user);
        $this->authorize('updateAdmin', $user);

        return view('admin.ppid.admins.form', compact('user'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $this->assertPpidAdmin($user);
        $this->authorize('updateAdmin', $user);

        $data = $this->validated($request, $user);
        $password = $data['password'] ?? null;
        unset($data['password'], $data['password_confirmation']);

        $user->name = $data['name'];
        $user->email = strtolower($data['email']);
        $user->username = $data['username'];

        if (filled($password)) {
            $user->password = Hash::make($password);
        }

        $user->save();

        ActivityLog::record('admin_updated', $user, 'Memperbarui Admin PPID: '.$user->email);

        return redirect()
            ->route('admin.ppid.admins.index')
            ->with('status', 'Admin PPID berhasil diperbarui.');
    }

    public function disable(Request $request, User $user): RedirectResponse
    {
        $this->assertPpidAdmin($user);
        $this->authorize('disableAdmin', $user);

        $user->is_active = false;
        $user->save();

        ActivityLog::record('admin_disabled', $user, 'Menonaktifkan Admin PPID: '.$user->email);

        return redirect()
            ->route('admin.ppid.admins.index')
            ->with('status', 'Admin PPID dinonaktifkan.');
    }

    public function enable(Request $request, User $user): RedirectResponse
    {
        $this->assertPpidAdmin($user);
        $this->authorize('updateAdmin', $user);

        $user->is_active = true;
        $user->save();

        ActivityLog::record('admin_updated', $user, 'Mengaktifkan Admin PPID: '.$user->email);

        return redirect()
            ->route('admin.ppid.admins.index')
            ->with('status', 'Admin PPID diaktifkan kembali.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        $this->assertPpidAdmin($user);
        $this->authorize('deleteAdmin', $user);

        ActivityLog::record('admin_deleted', $user, 'Menghapus Admin PPID: '.$user->email);
        $user->delete();

        return redirect()
            ->route('admin.ppid.admins.index')
            ->with('status', 'Admin PPID berhasil dihapus.');
    }

    public function bulkDestroy(Request $request): RedirectResponse
    {
        $this->authorize('createAdmin', User::class);

        $ids = $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer', 'exists:users,id'],
        ])['ids'];

        $targets = User::query()
            ->whereIn('id', $ids)
            ->get();

        $deleted = 0;
        $skipped = 0;

        foreach ($targets as $target) {
            if ($this->admins->isProtectedAdmin($target) || $request->user()->cannot('deleteAdmin', $target)) {
                $skipped++;

                continue;
            }

            ActivityLog::record('admin_deleted', $target, 'Menghapus Admin PPID (bulk): '.$target->email);
            $target->delete();
            $deleted++;
        }

        if ($deleted === 0 && $skipped > 0) {
            abort(403, 'Akun Admin PPID inti tidak dapat dihapus.');
        }

        return redirect()
            ->route('admin.ppid.admins.index')
            ->with('status', $deleted.' admin dihapus. '.$skipped.' akun terlindungi dilewati.');
    }

    private function assertPpidAdmin(User $user): void
    {
        if (! $user->hasRole(UserRole::AdminPpid)) {
            abort(404);
        }
    }

    /** @return array<string, mixed> */
    private function validated(Request $request, ?User $user = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => [
                'required',
                'string',
                'max:64',
                'regex:'.PpidAdminService::USERNAME_PATTERN,
                Rule::unique('users', 'username')->ignore($user?->id),
            ],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user?->id)],
            'password' => [$user ? 'nullable' : 'required', 'confirmed', Password::defaults()],
        ]);
    }
}
