<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    use HasFactory;

    protected $table = 'conversations';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'conversation_id',
        'user_id',
        'session_id',
        'sender',
        'message',
    ];

    /**
     * Get the user who owns this conversation message.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope to fetch all messages in a specific conversation.
     */
    public function scopeByConversation($query, $conversationId)
    {
        return $query->where('conversation_id', $conversationId)->orderBy('created_at');
    }

    /**
     * Scope to fetch all messages for a specific session.
     */
    public function scopeBySession($query, $sessionId)
    {
        return $query->where('session_id', $sessionId)->orderBy('created_at');
    }
}
