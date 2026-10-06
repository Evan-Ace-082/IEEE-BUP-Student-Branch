<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Achievement;
use App\Models\ActivityLog;
use App\Models\Announcement;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\ExecutiveCommittee;
use App\Models\MemberProfile;
use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function index()
    {
        $membersByDepartment = MemberProfile::query()
            ->selectRaw('department, count(*) as total')
            ->whereNotNull('department')
            ->groupBy('department')
            ->orderByDesc('total')
            ->get();

        $registrationsByEvent = Event::query()
            ->withCount([
                'registrations',
                'registrations as attended_count' => fn ($q) => $q->where('status', 'attended'),
            ])
            ->latest('starts_at')
            ->limit(20)
            ->get();

        $achievements = Achievement::query()
            ->selectRaw('category, count(*) as total')
            ->groupBy('category')
            ->pluck('total', 'category');

        $announcements = Announcement::query()
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $committees = ExecutiveCommittee::query()->withCount('members')->orderByDesc('year')->get();

        $adminActivity = ActivityLog::query()
            ->selectRaw('action, count(*) as total')
            ->groupBy('action')
            ->orderByDesc('total')
            ->get();

        return view('admin.reports.index', compact(
            'membersByDepartment',
            'registrationsByEvent',
            'achievements',
            'announcements',
            'committees',
            'adminActivity'
        ));
    }

    public function exportMembers(): StreamedResponse
    {
        $members = User::members()->with('profile')->orderBy('name')->get();

        return $this->csv('members.csv', ['Name', 'Email', 'Status', 'Student ID', 'Department', 'Batch', 'IEEE status'], function ($out) use ($members) {
            foreach ($members as $member) {
                fputcsv($out, [
                    $member->name,
                    $member->email,
                    $member->status,
                    $member->profile?->student_id,
                    $member->profile?->department,
                    $member->profile?->batch,
                    $member->profile?->ieee_membership_status,
                ]);
            }
        });
    }

    public function exportRegistrations(Request $request): StreamedResponse
    {
        $rows = EventRegistration::query()->with('event')->latest()->limit(5000)->get();

        return $this->csv('registrations.csv', ['Event', 'Name', 'Email', 'Status', 'Created'], function ($out) use ($rows) {
            foreach ($rows as $row) {
                fputcsv($out, [
                    $row->event?->title,
                    $row->name,
                    $row->email,
                    $row->status,
                    $row->created_at?->toDateTimeString(),
                ]);
            }
        });
    }

    private function csv(string $filename, array $header, callable $writer): StreamedResponse
    {
        return response()->streamDownload(function () use ($header, $writer) {
            $out = fopen('php://output', 'w');
            fputcsv($out, $header);
            $writer($out);
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv']);
    }
}
