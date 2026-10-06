<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Http\Requests\Member\UpdateProfileRequest;
use App\Services\ActivityLogger;
use App\Services\SecureUpload;
use App\Support\SettingsStore;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function edit(Request $request)
    {
        $user = $request->user();
        $profile = $user->profile()->firstOrCreate([]);

        return view('member.profile', compact('user', 'profile'));
    }

    public function update(UpdateProfileRequest $request)
    {
        $user = $request->user();
        $profile = $user->profile()->firstOrCreate([]);
        $this->authorize('update', $profile);

        $data = $request->validated();

        $user->name = $data['name'];
        $user->save();

        if ($request->boolean('remove_photo') && $profile->photo) {
            SecureUpload::delete($profile->photo);
            $profile->photo = null;
        }

        if ($request->file('photo')) {
            SecureUpload::delete($profile->photo);
            $profile->photo = SecureUpload::image($request->file('photo'), 'avatars');
        }

        $profile->fill([
            'student_id' => ($data['student_id'] ?? null) ?: null,
            'department' => ($data['department'] ?? null) ?: null,
            'batch' => ($data['batch'] ?? null) ?: null,
            'session' => ($data['session'] ?? null) ?: null,
            'ieee_membership_id' => ($data['ieee_membership_id'] ?? null) ?: null,
            'ieee_membership_status' => $data['ieee_membership_status'],
            'skills' => csv_list($data['skills'] ?? ''),
            'interests' => csv_list($data['interests'] ?? ''),
            'bio' => ($data['bio'] ?? null) ?: null,
            'linkedin' => safe_url($data['linkedin'] ?? null),
            'facebook' => safe_url($data['facebook'] ?? null),
            'phone' => ($data['phone'] ?? null) ?: null,
            'show_email' => $request->boolean('show_email'),
            'show_phone' => $request->boolean('show_phone'),
            'show_social' => $request->boolean('show_social'),
            'directory_visible' => $request->boolean('directory_visible'),
        ]);
        $profile->save();

        ActivityLogger::log('update', 'members', $user->id, 'Updated own profile');
        SettingsStore::bust();

        return back()->with('status', 'Your profile was saved.');
    }
}
