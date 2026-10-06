<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\AccountApprovedNotification;
use App\Services\ActivityLogger;
use App\Services\SessionGuard;
use App\Support\SettingsStore;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MemberController extends Controller
{
    public function index(Request $request)
    {
        $query = User::members()->with('profile')->latest();

        if ($search = trim($request->string('q')->toString())) {
            $query->where(function ($inner) use ($search) {
                $inner->where('name', 'like', like_term($search))
                    ->orWhere('email', 'like', like_term($search))
                    ->orWhereHas('profile', fn ($profile) => $profile->where('student_id', 'like', like_term($search)));
            });
        }

        if ($status = $request->string('status')->toString()) {
            $query->where('status', $status);
        }

        return view('admin.members.index', [
            'members' => $query->paginate(15)->withQueryString(),
        ]);
    }

    public function edit(User $member)
    {
        abort_unless($member->isMember(), 404);
        $member->load('profile');

        return view('admin.members.edit', ['member' => $member]);
    }

    public function update(Request $request, User $member)
    {
        abort_unless($member->isMember(), 404);
        $profile = $member->profile()->firstOrCreate([]);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'status' => ['required', Rule::in(['pending', 'active', 'suspended', 'inactive'])],
            'department' => ['nullable', 'string', 'max:120'],
            'batch' => ['nullable', 'string', 'max:40'],
            'student_id' => ['nullable', 'string', 'max:40', Rule::unique('member_profiles', 'student_id')->ignore($profile->id)],
        ]);

        $previous = $member->status;
        $member->name = $data['name'];
        $member->status = $data['status'];

        if ($data['status'] === 'active' && $previous !== 'active') {
            $member->approved_at = now();
            $member->approved_by = $request->user()->id;
        }

        $member->save();
        $profile->department = $data['department'] ?: null;
        $profile->batch = $data['batch'] ?: null;
        $profile->student_id = $data['student_id'] ?: null;
        $profile->save();

        if (in_array($data['status'], ['suspended', 'inactive'], true)) {
            SessionGuard::invalidateUser($member->id);
            ActivityLogger::log('suspend', 'members', $member->id, 'Member set to '.$data['status']);
        } elseif ($previous !== 'active' && $data['status'] === 'active') {
            ActivityLogger::log('approve', 'members', $member->id, 'Approved member');
            if ($member->hasVerifiedEmail()) {
                $member->notify(new AccountApprovedNotification);
            }
        } else {
            ActivityLogger::log('update', 'members', $member->id, 'Updated member');
        }

        SettingsStore::bust();

        return redirect()->route('admin.members.edit', $member)->with('status', 'Member saved.');
    }

    public function approve(Request $request, User $member)
    {
        abort_unless($member->isMember(), 404);
        $member->status = 'active';
        $member->approved_at = now();
        $member->approved_by = $request->user()->id;
        $member->save();
        ActivityLogger::log('approve', 'members', $member->id, 'Approved member');
        if ($member->hasVerifiedEmail()) {
            $member->notify(new AccountApprovedNotification);
        }
        SettingsStore::bust();

        return back()->with('status', $member->name.' is now active.');
    }

    public function suspend(User $member)
    {
        abort_unless($member->isMember(), 404);
        $member->status = 'suspended';
        $member->save();
        SessionGuard::invalidateUser($member->id);
        ActivityLogger::log('suspend', 'members', $member->id, 'Suspended member');
        SettingsStore::bust();

        return back()->with('status', $member->name.' was suspended.');
    }
}
