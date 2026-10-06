<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMembershipApplicationRequest;
use App\Models\MembershipApplication;
use App\Services\ActivityLogger;

class MembershipController extends Controller
{
    public function index()
    {
        return view('public.membership');
    }

    public function apply(StoreMembershipApplicationRequest $request)
    {
        $data = $request->safe()->only([
            'name', 'student_id', 'email', 'phone', 'department', 'batch', 'ieee_membership_status', 'message',
        ]);
        $data['email'] = mb_strtolower($data['email']);
        foreach (['student_id', 'phone', 'department', 'batch', 'message'] as $field) {
            $data[$field] = ($data[$field] ?? '') === '' ? null : $data[$field];
        }
        $data['user_id'] = $request->user()?->id;
        $data['status'] = 'submitted';

        $application = MembershipApplication::query()->create($data);
        ActivityLogger::log('create', 'membership_applications', $application->id, 'Membership interest submitted');

        return redirect()->route('membership')->with('status', 'Your interest form was received. This does not create an IEEE membership.');
    }
}
