<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\EventRegistration;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;

class RegistrationController extends Controller
{
    public function index(Request $request)
    {
        $registrations = EventRegistration::query()
            ->with('event')
            ->where('user_id', $request->user()->id)
            ->latest()
            ->paginate(12);

        return view('member.registrations', compact('registrations'));
    }

    public function cancel(Request $request, EventRegistration $registration)
    {
        if ($registration->user_id !== $request->user()->id) {
            abort(403);
        }

        if ($registration->status !== 'registered') {
            return back()->withErrors(['status' => 'This registration can no longer be cancelled.']);
        }

        $registration->status = 'cancelled';
        $registration->save();
        ActivityLogger::log('update', 'event_registrations', $registration->id, 'Member cancelled registration');

        return back()->with('status', 'Your registration was cancelled.');
    }
}
