<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SuperAdmin\AdminUserRequest;
use App\Models\Role;
use App\Models\User;
use App\Services\ActivityLogger;
use App\Services\SessionGuard;

class AdminUserController extends Controller
{
    public function index()
    {
        $this->authorize('viewAnyAdmin', User::class);

        $admins = User::admins()->latest()->paginate(15);

        return view('super.admins.index', compact('admins'));
    }

    public function create()
    {
        $this->authorize('createAdmin', User::class);

        return view('super.admins.form', ['adminUser' => new User(['status' => 'active'])]);
    }

    public function store(AdminUserRequest $request)
    {
        $adminRoleId = Role::query()->where('slug', 'admin')->value('id');

        $admin = new User;
        $admin->name = $request->string('name')->toString();
        $admin->email = mb_strtolower($request->string('email')->toString());
        $admin->password = $request->string('password')->toString();
        $admin->role_id = $adminRoleId;
        $admin->status = $request->string('status')->toString();
        $admin->email_verified_at = now();
        $admin->approved_at = now();
        $admin->approved_by = $request->user()->id;
        $admin->save();

        ActivityLogger::log('create', 'admins', $admin->id, 'Created administrator '.$admin->email);

        return redirect()->route('super.admins.index')->with('status', 'Administrator created. Share the password with them directly. It is not emailed.');
    }

    public function show(User $adminUser)
    {
        $this->authorize('manageAdmin', $adminUser);
        $logs = $adminUser->activityLogs()->latest('created_at')->limit(20)->get();

        return view('super.admins.show', ['adminUser' => $adminUser, 'logs' => $logs]);
    }

    public function edit(User $adminUser)
    {
        $this->authorize('manageAdmin', $adminUser);

        return view('super.admins.form', compact('adminUser'));
    }

    public function update(AdminUserRequest $request, User $adminUser)
    {
        $this->authorize('manageAdmin', $adminUser);

        $previous = $adminUser->status;
        $adminUser->name = $request->string('name')->toString();
        $adminUser->email = mb_strtolower($request->string('email')->toString());
        $adminUser->status = $request->string('status')->toString();

        if ($request->filled('password')) {
            $adminUser->password = $request->string('password')->toString();
        }

        if ($request->has('role') && $request->input('role') !== 'admin') {
            ActivityLogger::log('update', 'admins', $adminUser->id, 'Blocked an attempt to change an administrator role');
        }

        $adminUser->save();

        if (in_array($adminUser->status, ['suspended', 'inactive'], true) && $previous !== $adminUser->status) {
            SessionGuard::invalidateUser($adminUser->id);
            ActivityLogger::log('suspend', 'admins', $adminUser->id, 'Administrator set to '.$adminUser->status);
        } elseif ($previous !== 'active' && $adminUser->status === 'active') {
            ActivityLogger::log('activate', 'admins', $adminUser->id, 'Administrator activated');
        } else {
            ActivityLogger::log('update', 'admins', $adminUser->id, 'Updated administrator');
        }

        return redirect()->route('super.admins.edit', $adminUser)->with('status', 'Administrator saved. The role remains Admin.');
    }

    public function activate(User $adminUser)
    {
        $this->authorize('manageAdmin', $adminUser);
        $adminUser->status = 'active';
        $adminUser->save();
        ActivityLogger::log('activate', 'admins', $adminUser->id, 'Administrator activated');

        return back()->with('status', $adminUser->name.' is active.');
    }

    public function suspend(User $adminUser)
    {
        $this->authorize('manageAdmin', $adminUser);
        $adminUser->status = 'suspended';
        $adminUser->save();
        SessionGuard::invalidateUser($adminUser->id);
        ActivityLogger::log('suspend', 'admins', $adminUser->id, 'Administrator suspended');

        return back()->with('status', $adminUser->name.' is suspended and signed out.');
    }

    public function destroy(User $adminUser)
    {
        $this->authorize('manageAdmin', $adminUser);
        $adminUser->status = 'inactive';
        $adminUser->save();
        SessionGuard::invalidateUser($adminUser->id);
        $adminUser->delete();
        ActivityLogger::log('delete', 'admins', $adminUser->id, 'Removed administrator. Content they created was kept.');

        return redirect()->route('super.admins.index')->with('status', 'Administrator removed. Their activity history and website content were kept.');
    }
}
