<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Models\MembershipApplication;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MessageController extends Controller
{
    public function index(Request $request)
    {
        $query = ContactMessage::query()->latest();

        if ($status = $request->string('status')->toString()) {
            $query->where('status', $status);
        }

        if ($search = trim($request->string('q')->toString())) {
            $query->where(function ($inner) use ($search) {
                $inner->where('name', 'like', like_term($search))
                    ->orWhere('email', 'like', like_term($search))
                    ->orWhere('subject', 'like', like_term($search));
            });
        }

        return view('admin.messages.index', [
            'messages' => $query->paginate(15)->withQueryString(),
            'applications' => MembershipApplication::query()->latest()->limit(8)->get(),
            'statuses' => ContactMessage::statuses(),
        ]);
    }

    public function update(Request $request, ContactMessage $message)
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(array_keys(ContactMessage::statuses()))],
        ]);

        $message->status = $data['status'];
        $message->save();
        ActivityLogger::log('update', 'contact_messages', $message->id, 'Marked message '.$data['status']);

        return back()->with('status', 'Message updated.');
    }
}
