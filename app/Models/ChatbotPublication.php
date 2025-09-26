<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ChatbotPublication extends Model {

    use HasFactory;

    protected $fillable = ['bot_id', 'payload', 'status', 'user_id', 'chatbot_id'];

    protected $casts = [
        'payload' => 'array', // automatically cast to array/json
    ];

    public function histories() {
        return $this->hasMany(ChatbotPublicationHistory::class, 'publication_id');
    }
}
