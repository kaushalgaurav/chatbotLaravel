<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UploadJob extends Model {
    protected $fillable = [
        'upload_uuid',
        'chatbot_id',
        'file_record_id',
        'total_rows',
        'processed_rows',
        'inserted',
        'updated',
        'status',
        'error'
    ];
}
