<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatbotPublicationHistory extends Model {
    use HasFactory;

    protected $fillable = [
        'publication_id',
        'old_payload',
        'new_payload',
        'changed_by',
        'version',
    ];

    protected $casts = [
        'old_payload' => 'array',
        'new_payload' => 'array',
    ];

    public function publication() {
        return $this->belongsTo(ChatbotPublication::class, 'publication_id');
    }
}
