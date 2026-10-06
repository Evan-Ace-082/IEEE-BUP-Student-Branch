<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $query = ActivityLog::query()->with('user')->latest('created_at');

        if (! $request->user()->isSuperAdmin()) {
            $query->whereNotIn('module', ['admins', 'settings']);
        }

        if ($module = trim($request->string('module')->toString())) {
            $query->where('module', $module);
        }

        if ($action = trim($request->string('action')->toString())) {
            $query->where('action', $action);
        }

        if ($search = trim($request->string('q')->toString())) {
            $query->where('description', 'like', like_term($search));
        }

        return view('admin.logs.index', [
            'logs' => $query->paginate(30)->withQueryString(),
        ]);
    }
}
