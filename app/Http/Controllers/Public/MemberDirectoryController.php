<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\MemberProfile;
use App\Models\User;
use Illuminate\Http\Request;

class MemberDirectoryController extends Controller
{
    public function index(Request $request)
    {
        $query = MemberProfile::query()
            ->with('user')
            ->where('directory_visible', true)
            ->whereHas('user', function ($user) {
                $user->where('status', 'active')->whereHas('role', fn ($role) => $role->where('slug', 'member'));
            });

        if ($name = trim($request->string('name')->toString())) {
            $query->whereHas('user', fn ($user) => $user->where('name', 'like', like_term($name)));
        }

        if (auth()->check() && ($studentId = trim($request->string('student_id')->toString()))) {
            $query->where('student_id', 'like', like_term($studentId));
        }

        foreach (['batch', 'department', 'ieee_membership_status'] as $field) {
            $value = trim($request->string($field)->toString());
            if ($value !== '') {
                $query->where($field, $value);
            }
        }

        if ($skill = trim($request->string('skill')->toString())) {
            $query->where('skills', 'like', like_term($skill));
        }

        if ($interest = trim($request->string('interest')->toString())) {
            $query->where('interests', 'like', like_term($interest));
        }

        $members = $query->orderBy('id')->paginate(12)->withQueryString();

        $departments = MemberProfile::query()->whereNotNull('department')->distinct()->orderBy('department')->pluck('department');
        $batches = MemberProfile::query()->whereNotNull('batch')->distinct()->orderBy('batch')->pluck('batch');

        return view('public.members.index', compact('members', 'departments', 'batches'));
    }

    public function show(User $user)
    {
        $user->load('profile');
        $profile = $user->profile;

        if (! $user->isMember() || ! $user->isActiveAccount() || ! $profile || ! $profile->directory_visible) {
            if (auth()->id() !== $user->id && ! auth()->user()?->hasAdminAccess()) {
                abort(404);
            }
        }

        return view('public.members.show', compact('user', 'profile'));
    }
}
