<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'label',
        'url',
        'location',
        'is_cta',
        'sort_order',
        'is_active',
        'target',
    ];

    protected $casts = [
        'is_cta' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];
}
