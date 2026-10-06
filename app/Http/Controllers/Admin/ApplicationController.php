<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MembershipApplication;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ApplicationController extends Controller
{
    public function update(Request $request, MembershipApplication $application)
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(['submitted', 'reviewing', 'closed'])],
        ]);

        $application->status = $data['status'];
        $application->save();
        ActivityLogger::log('update', 'membership_applications', $application->id, 'Updated interest form status');

        return back()->with('status', 'Interest form updated.');
    }
}
