<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Services\ActivityLogger;
use App\Services\SecureUpload;
use App\Support\SettingsStore;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function edit()
    {
        $settings = [];
        foreach (array_keys(SettingsStore::settingDefaults()) as $key) {
            $settings[$key] = setting($key);
        }

        return view('super.settings', compact('settings'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'branch_name' => ['required', 'string', 'max:160'],
            'tagline' => ['required', 'string', 'max:255'],
            'official_email' => ['nullable', 'email', 'max:160'],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:255'],
            'facebook' => ['nullable', 'url', 'max:255'],
            'linkedin' => ['nullable', 'url', 'max:255'],
            'instagram' => ['nullable', 'url', 'max:255'],
            'website' => ['nullable', 'url', 'max:255'],
            'ieee_join_url' => ['required', 'url', 'max:255'],
            'map_embed_url' => ['nullable', 'url', 'max:500'],
            'logo' => ['nullable', 'file', 'max:2048', 'mimes:jpg,jpeg,png,webp,gif'],
            'favicon' => ['nullable', 'file', 'max:1024', 'mimes:jpg,jpeg,png,webp,gif'],
        ]);

        if (($data['map_embed_url'] ?? '') !== '' && safe_embed($data['map_embed_url']) === null) {
            return back()->withErrors(['map_embed_url' => 'Use an https embed from Google Maps or OpenStreetMap.'])->withInput();
        }

        if (safe_url($data['ieee_join_url']) === null) {
            return back()->withErrors(['ieee_join_url' => 'Enter a valid http or https link.'])->withInput();
        }

        foreach (['branch_name', 'tagline', 'official_email', 'phone', 'address', 'facebook', 'linkedin', 'instagram', 'website', 'ieee_join_url', 'map_embed_url'] as $key) {
            SettingsStore::putSetting($key, $data[$key] ?: null, in_array($key, ['branch_name', 'tagline', 'logo', 'favicon', 'ieee_join_url'], true) ? 'system' : 'contact');
        }

        if ($request->file('logo')) {
            SecureUpload::delete(setting('logo') ?: null);
            SettingsStore::putSetting('logo', SecureUpload::image($request->file('logo'), 'settings'), 'system');
        }

        if ($request->file('favicon')) {
            SecureUpload::delete(setting('favicon') ?: null);
            SettingsStore::putSetting('favicon', SecureUpload::image($request->file('favicon'), 'settings'), 'system');
        }

        SettingsStore::bust();
        ActivityLogger::log('update', 'settings', null, 'Updated system settings');

        return back()->with('status', 'System settings saved.');
    }
}
