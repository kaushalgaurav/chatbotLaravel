<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class File extends Model {
    protected $fillable = ['chatbot_id', 'file_name', 'file_path', 'file_type', 'file_size'];
}
