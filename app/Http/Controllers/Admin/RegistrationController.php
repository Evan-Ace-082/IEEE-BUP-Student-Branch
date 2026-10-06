<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class RegistrationController extends Controller
{
    public function index(Request $request)
    {
        $registrations = $this->filtered($request)->paginate(20)->withQueryString();

        return view('admin.registrations.index', [
            'registrations' => $registrations,
            'events' => Event::query()->orderByDesc('starts_at')->get(['id', 'title']),
            'statuses' => EventRegistration::statuses(),
        ]);
    }

    public function update(Request $request, EventRegistration $registration)
    {
        $data = $request->validate([
            'status' => ['required', 'in:registered,cancelled,attended,no_show'],
        ]);

        $registration->status = $data['status'];
        $registration->save();
        ActivityLogger::log('update', 'event_registrations', $registration->id, 'Set registration status to '.$data['status']);

        return back()->with('status', 'Registration updated.');
    }

    public function export(Request $request): StreamedResponse
    {
        $rows = $this->filtered($request)->get();

        return response()->streamDownload(function () use ($rows) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Event', 'Name', 'Student ID', 'Email', 'Phone', 'Department', 'Batch', 'IEEE status', 'Registration status', 'Registered at']);
            foreach ($rows as $row) {
                fputcsv($out, [
                    $row->event?->title,
                    $row->name,
                    $row->student_id,
                    $row->email,
                    $row->phone,
                    $row->department,
                    $row->batch,
                    $row->ieee_membership_status,
                    $row->status,
                    $row->created_at?->toDateTimeString(),
                ]);
            }
            fclose($out);
        }, 'event-registrations.csv', ['Content-Type' => 'text/csv']);
    }

    private function filtered(Request $request)
    {
        $query = EventRegistration::query()->with('event')->latest();

        if ($eventId = $request->integer('event_id')) {
            $query->where('event_id', $eventId);
        }

        if ($status = $request->string('status')->toString()) {
            $query->where('status', $status);
        }

        if ($search = trim($request->string('q')->toString())) {
            $query->where(function ($inner) use ($search) {
                $inner->where('name', 'like', like_term($search))
                    ->orWhere('email', 'like', like_term($search))
                    ->orWhere('student_id', 'like', like_term($search));
            });
        }

        return $query;
    }
}
