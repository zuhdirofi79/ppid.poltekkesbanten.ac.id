<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        return view('admin.profile.edit', [
            'user' => $request->user(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'password' => ['nullable', 'confirmed', Password::defaults()],
        ]);

        $user->name = $data['name'];

        if (filled($data['password'] ?? null)) {
            $user->password = Hash::make($data['password']);
        }

        $user->save();

        ActivityLog::record('admin_updated', $user, 'Memperbarui profil sendiri');

        return redirect()
            ->route('admin.profile.edit')
            ->with('status', 'Profil berhasil diperbarui.');
    }
}
