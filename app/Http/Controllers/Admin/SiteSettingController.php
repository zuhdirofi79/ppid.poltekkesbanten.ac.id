<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\SiteSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SiteSettingController extends Controller
{
    public function edit(): View
    {
        $settings = SiteSetting::query()->orderBy('group')->orderBy('key')->get()->groupBy('group');

        return view('admin.settings.edit', compact('settings'));
    }

    public function update(Request $request): RedirectResponse
    {
        $payload = $request->validate([
            'settings' => ['required', 'array'],
            'settings.*' => ['nullable', 'string', 'max:5000'],
        ]);

        foreach ($payload['settings'] as $key => $value) {
            $setting = SiteSetting::query()->where('key', $key)->first();
            if (! $setting) {
                continue;
            }

            $setting->update(['value' => $value]);
        }

        SiteSetting::forgetCache();
        ActivityLog::record('updated', null, 'Memperbarui pengaturan situs');

        return redirect()->route('admin.settings.edit')->with('status', 'Pengaturan disimpan.');
    }
}
