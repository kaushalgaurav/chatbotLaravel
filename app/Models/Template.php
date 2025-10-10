<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Template extends Model {
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'category',
        'is_active',
    ];

    public function publications() {
        return $this->hasMany(Publication::class);
    }
}
