<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductJson extends Model {

    use HasFactory;

    // protected $table = 'chatbot_product_json';
    protected $fillable = [
        'chatbot_id',
        'products' // JSON stored as string
    ];

    protected $casts = [
        'products' => 'array'
    ];
}
