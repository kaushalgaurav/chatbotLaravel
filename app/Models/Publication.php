<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Publication extends Model {

    use HasFactory;

    protected $fillable = ['bot_id', 'payload', 'status', 'user_id', 'chatbot_id', 'is_published'];

    protected $casts = [
        'payload' => 'array', // automatically cast to array/json
    ];

    public function histories() {
        return $this->hasMany(PublicationHistory::class, 'publication_id');
    }
}
