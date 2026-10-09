<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\LeadershipProfileRequest;
use App\Models\LeadershipProfile;
use App\Services\ActivityLogger;
use App\Services\SecureUpload;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class LeadershipProfileController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', LeadershipProfile::class);

        return view('admin.leadership.index', [
            'profiles' => LeadershipProfile::query()->orderBy('sort_order')->orderBy('id')->paginate(20),
            'roles' => LeadershipProfile::roleOptions(),
        ]);
    }

    public function create()
    {
        $this->authorize('create', LeadershipProfile::class);

        $next = (int) LeadershipProfile::query()->max('sort_order') + 1;

        return view('admin.leadership.form', [
            'profile' => new LeadershipProfile([
                'role' => 'chairman_faculty_advisor',
                'sort_order' => $next,
                'is_published' => false,
            ]),
            'roles' => LeadershipProfile::roleOptions(),
        ]);
    }

    public function store(LeadershipProfileRequest $request)
    {
        $this->authorize('create', LeadershipProfile::class);

        $data = $this->payload($request);
        $data['photo'] = $request->file('photo') ? $this->storePhoto($request) : null;
        $data['updated_by'] = $request->user()->id;

        $profile = LeadershipProfile::query()->create($data);
        ActivityLogger::log('create', 'leadership', $profile->id, 'Created leadership profile');

        return redirect()->route('admin.leadership.edit', $profile)->with('status', 'Leadership profile created.');
    }

    public function edit(LeadershipProfile $profile)
    {
        $this->authorize('update', $profile);

        return view('admin.leadership.form', [
            'profile' => $profile,
            'roles' => LeadershipProfile::roleOptions(),
        ]);
    }

    public function update(LeadershipProfileRequest $request, LeadershipProfile $profile)
    {
        $this->authorize('update', $profile);

        $data = $this->payload($request);
        $data['photo'] = $profile->photo;
        $data['updated_by'] = $request->user()->id;

        if ($request->boolean('remove_photo') && $profile->photo) {
            SecureUpload::delete($profile->photo);
            $data['photo'] = null;
        }

        if ($request->file('photo')) {
            SecureUpload::delete($data['photo']);
            $data['photo'] = $this->storePhoto($request);
        }

        $profile->update($data);
        ActivityLogger::log('update', 'leadership', $profile->id, 'Updated leadership profile');

        return redirect()->route('admin.leadership.edit', $profile)->with('status', 'Leadership profile saved.');
    }

    public function destroy(LeadershipProfile $profile)
    {
        $this->authorize('delete', $profile);

        SecureUpload::delete($profile->photo);
        $profile->delete();
        ActivityLogger::log('delete', 'leadership', $profile->id, 'Deleted leadership profile');

        return redirect()->route('admin.leadership.index')->with('status', 'Leadership profile deleted.');
    }

    private function payload(LeadershipProfileRequest $request): array
    {
        $data = $request->safe()->only(['role', 'name', 'designation', 'bio', 'email', 'phone', 'sort_order']);
        $data['designation'] = $data['designation'] ?: null;
        $data['bio'] = $data['bio'] ?: null;
        $data['email'] = $data['email'] ?: null;
        $data['phone'] = $data['phone'] ?: null;
        $data['show_email'] = $request->boolean('show_email');
        $data['show_phone'] = $request->boolean('show_phone');
        $data['is_published'] = $request->boolean('is_published');

        return $data;
    }

    private function storePhoto(Request $request): string
    {
        try {
            return SecureUpload::image($request->file('photo'), 'leadership');
        } catch (ValidationException $exception) {
            $message = $exception->validator->errors()->first('file')
                ?: $exception->validator->errors()->first()
                ?: 'The photo could not be uploaded.';

            throw ValidationException::withMessages(['photo' => $message]);
        }
    }
}
