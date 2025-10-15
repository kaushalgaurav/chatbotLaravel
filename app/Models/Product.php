<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model {

    use HasFactory;

    protected $fillable = [
        'chatbot_id',
        'product_name',
        'product_unique_id',
        'product_image',
        'description',
        'price',
        'tags',
        'product_link'
    ];
}
