<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CommitteeMember;
use App\Models\CommitteePosition;
use App\Models\ExecutiveCommittee;
use App\Services\ActivityLogger;
use App\Services\SecureUpload;
use App\Support\SettingsStore;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CommitteeController extends Controller
{
    public function index()
    {
        $committees = ExecutiveCommittee::query()->withCount('members')->orderByDesc('year')->get();
        $positions = CommitteePosition::query()->orderBy('sort_order')->orderBy('name')->get();

        return view('admin.committee.index', compact('committees', 'positions'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:160'],
            'year' => ['required', 'integer', 'min:2000', 'max:2100'],
            'description' => ['nullable', 'string', 'max:2000'],
            'is_current' => ['nullable', 'boolean'],
        ]);

        if ($request->boolean('is_current')) {
            ExecutiveCommittee::query()->update(['is_current' => false]);
        }

        $committee = ExecutiveCommittee::query()->create([
            'name' => $data['name'],
            'year' => $data['year'],
            'description' => $data['description'] ?: null,
            'is_current' => $request->boolean('is_current'),
        ]);

        ActivityLogger::log('create', 'committee', $committee->id, 'Created committee '.$committee->name);
        SettingsStore::bust();

        return redirect()->route('admin.committee.show', $committee)->with('status', 'Committee created. Earlier committees stay in the archive.');
    }

    public function show(ExecutiveCommittee $committee)
    {
        $committee->load(['members.position']);
        $positions = CommitteePosition::query()->orderBy('sort_order')->get();

        return view('admin.committee.show', compact('committee', 'positions'));
    }

    public function update(Request $request, ExecutiveCommittee $committee)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:160'],
            'year' => ['required', 'integer', 'min:2000', 'max:2100'],
            'description' => ['nullable', 'string', 'max:2000'],
            'is_current' => ['nullable', 'boolean'],
        ]);

        if ($request->boolean('is_current')) {
            ExecutiveCommittee::query()->where('id', '!=', $committee->id)->update(['is_current' => false]);
        }

        $committee->update([
            'name' => $data['name'],
            'year' => $data['year'],
            'description' => $data['description'] ?: null,
            'is_current' => $request->boolean('is_current'),
        ]);

        ActivityLogger::log('update', 'committee', $committee->id, 'Updated committee');
        SettingsStore::bust();

        return back()->with('status', 'Committee saved.');
    }

    public function destroy(ExecutiveCommittee $committee)
    {
        $committee->delete();
        ActivityLogger::log('delete', 'committee', $committee->id, 'Archived committee');
        SettingsStore::bust();

        return redirect()->route('admin.committee.index')->with('status', 'Committee archived. It was not permanently erased from the database.');
    }

    public function storeMember(Request $request, ExecutiveCommittee $committee)
    {
        $data = $this->memberData($request);
        $member = $committee->members()->create($data);

        if ($request->file('photo')) {
            $member->photo = SecureUpload::image($request->file('photo'), 'committee');
            $member->save();
        }

        ActivityLogger::log('create', 'committee', $member->id, 'Added committee member');
        SettingsStore::bust();

        return back()->with('status', 'Committee member added.');
    }

    public function updateMember(Request $request, CommitteeMember $committeeMember)
    {
        $committeeMember->update($this->memberData($request));

        if ($request->boolean('remove_photo') && $committeeMember->photo) {
            SecureUpload::delete($committeeMember->photo);
            $committeeMember->photo = null;
            $committeeMember->save();
        }

        if ($request->file('photo')) {
            SecureUpload::delete($committeeMember->photo);
            $committeeMember->photo = SecureUpload::image($request->file('photo'), 'committee');
            $committeeMember->save();
        }

        ActivityLogger::log('update', 'committee', $committeeMember->id, 'Updated committee member');
        SettingsStore::bust();

        return back()->with('status', 'Committee member saved.');
    }

    public function destroyMember(CommitteeMember $committeeMember)
    {
        SecureUpload::delete($committeeMember->photo);
        $committeeMember->delete();
        ActivityLogger::log('delete', 'committee', $committeeMember->id, 'Removed committee member');
        SettingsStore::bust();

        return back()->with('status', 'Committee member removed from this term.');
    }

    public function storePosition(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:999'],
        ]);

        $position = CommitteePosition::query()->create([
            'name' => $data['name'],
            'slug' => unique_slug(CommitteePosition::class, $data['name']),
            'sort_order' => $data['sort_order'] ?? 0,
        ]);

        ActivityLogger::log('create', 'committee', $position->id, 'Created position '.$position->name);

        return back()->with('status', 'Position added. It can be reused by future committees.');
    }

    public function updatePosition(Request $request, CommitteePosition $position)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:999'],
        ]);

        $position->update([
            'name' => $data['name'],
            'slug' => unique_slug(CommitteePosition::class, $data['name'], $position->id),
            'sort_order' => $data['sort_order'] ?? 0,
        ]);

        ActivityLogger::log('update', 'committee', $position->id, 'Updated position');
        SettingsStore::bust();

        return back()->with('status', 'Position saved.');
    }

    public function destroyPosition(CommitteePosition $position)
    {
        if ($position->members()->exists()) {
            return back()->withErrors(['position' => 'This position is used by committee records, so it cannot be deleted.']);
        }

        $position->delete();
        ActivityLogger::log('delete', 'committee', $position->id, 'Deleted unused position');

        return back()->with('status', 'Position removed.');
    }

    private function memberData(Request $request): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'committee_position_id' => ['required', Rule::exists('committee_positions', 'id')],
            'department' => ['nullable', 'string', 'max:120'],
            'batch' => ['nullable', 'string', 'max:40'],
            'bio' => ['nullable', 'string', 'max:2000'],
            'linkedin' => ['nullable', 'url', 'max:255'],
            'facebook' => ['nullable', 'url', 'max:255'],
            'other_link' => ['nullable', 'url', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:999'],
            'photo' => ['nullable', 'file', 'max:2048', 'mimes:jpg,jpeg,png,webp,gif'],
            'remove_photo' => ['nullable', 'boolean'],
        ]);

        return [
            'name' => $data['name'],
            'committee_position_id' => $data['committee_position_id'],
            'department' => $data['department'] ?: null,
            'batch' => $data['batch'] ?: null,
            'bio' => $data['bio'] ?: null,
            'linkedin' => safe_url($data['linkedin'] ?? null),
            'facebook' => safe_url($data['facebook'] ?? null),
            'other_link' => safe_url($data['other_link'] ?? null),
            'sort_order' => $data['sort_order'] ?? 0,
        ];
    }
}
