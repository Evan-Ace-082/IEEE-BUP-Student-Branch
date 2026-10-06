<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PageContent;
use App\Services\ActivityLogger;
use App\Support\SettingsStore;
use Illuminate\Http\Request;

class ContentController extends Controller
{
    public function edit()
    {
        $defaults = SettingsStore::contentDefaults();
        $stored = PageContent::query()->get()->keyBy('key');

        $contents = collect($defaults)->map(function ($meta, $key) use ($stored) {
            return [
                'key' => $key,
                'title' => $meta['title'],
                'group' => $meta['group'],
                'body' => $stored[$key]->body ?? $meta['body'],
            ];
        })->groupBy('group');

        $contactKeys = ['official_email', 'phone', 'address', 'facebook', 'linkedin', 'instagram', 'website', 'map_embed_url'];
        $contact = [];
        foreach ($contactKeys as $key) {
            $contact[$key] = setting($key);
        }

        return view('admin.content.edit', compact('contents', 'contact'));
    }

    public function update(Request $request)
    {
        $defaults = SettingsStore::contentDefaults();
        $bodies = $request->input('bodies', []);

        if (! is_array($bodies)) {
            $bodies = [];
        }

        foreach ($defaults as $key => $meta) {
            if (! array_key_exists($key, $bodies)) {
                continue;
            }

            PageContent::query()->updateOrCreate(
                ['key' => $key],
                [
                    'title' => $meta['title'],
                    'group' => $meta['group'],
                    'body' => is_string($bodies[$key]) ? $bodies[$key] : '',
                    'updated_by' => $request->user()->id,
                ]
            );
        }

        $contact = $request->validate([
            'official_email' => ['nullable', 'email', 'max:160'],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:255'],
            'facebook' => ['nullable', 'url', 'max:255'],
            'linkedin' => ['nullable', 'url', 'max:255'],
            'instagram' => ['nullable', 'url', 'max:255'],
            'website' => ['nullable', 'url', 'max:255'],
            'map_embed_url' => ['nullable', 'url', 'max:500'],
        ]);

        if (($contact['map_embed_url'] ?? '') !== '' && safe_embed($contact['map_embed_url']) === null) {
            return back()->withErrors(['map_embed_url' => 'Use an https embed from Google Maps or OpenStreetMap.'])->withInput();
        }

        foreach ($contact as $key => $value) {
            SettingsStore::putSetting($key, $value ?: null, 'contact');
        }

        SettingsStore::bust();
        ActivityLogger::log('update', 'content', null, 'Updated website content');

        return back()->with('status', 'Website content saved.');
    }
}
