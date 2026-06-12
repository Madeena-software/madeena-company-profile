<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'slug', 'tagline', 'content_json',
        'specifications', 'image_path', 'is_featured',
        'is_active', 'sort_order',
    ];

    protected $casts = [
        'content_json' => 'array',
        'specifications' => 'array',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
    ];
}
