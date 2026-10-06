<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactMessage extends Model
{
    protected $fillable = [
        'name',
        'email',
        'subject',
        'message',
        'status',
        'ip_address',
    ];

    public static function statuses(): array
    {
        return [
            'unread' => 'Unread',
            'read' => 'Read',
            'replied' => 'Replied',
            'archived' => 'Archived',
        ];
    }
}
