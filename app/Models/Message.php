<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'subject',
        'message',
        'is_read',
        'admin_reply',
        'replied_at',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'replied_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeReplied($query)
    {
        return $query->whereNotNull('admin_reply');
    }

    public function scopeUnreplied($query)
    {
        return $query->whereNull('admin_reply');
    }

    public function markAsRead()
    {
        $this->update(['is_read' => true]);
    }
}