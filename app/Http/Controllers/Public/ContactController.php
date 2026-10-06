<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreContactRequest;
use App\Models\ContactMessage;
use App\Models\User;
use App\Notifications\ContactMessageNotification;
use App\Services\ActivityLogger;
use Illuminate\Support\Facades\Notification;

class ContactController extends Controller
{
    public function index()
    {
        return view('public.contact');
    }

    public function store(StoreContactRequest $request)
    {
        $message = ContactMessage::query()->create([
            'name' => $request->string('name')->toString(),
            'email' => mb_strtolower($request->string('email')->toString()),
            'subject' => $request->string('subject')->toString(),
            'message' => $request->string('message')->toString(),
            'status' => 'unread',
            'ip_address' => $request->ip(),
        ]);

        $admins = User::query()
            ->where('status', 'active')
            ->whereHas('role', fn ($role) => $role->whereIn('slug', ['admin', 'super_admin']))
            ->get();

        if ($admins->isNotEmpty()) {
            Notification::send($admins, new ContactMessageNotification($message));
        }

        $email = setting('official_email');
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Notification::route('mail', $email)->notify(new ContactMessageNotification($message, true));
        }

        ActivityLogger::log('create', 'contact_messages', $message->id, 'Contact message received');

        return redirect()->route('contact')->with('status', 'Your message was sent. The branch will read it from the admin dashboard.');
    }
}
