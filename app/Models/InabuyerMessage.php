<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InabuyerMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'organization',
        'kesan_dan_pesan',
        'is_visible',
    ];

    protected $casts = [
        'is_visible' => 'boolean',
    ];

    public function scopeVisible($query)
    {
        return $query->where('is_visible', true);
    }

    public function scopeHidden($query)
    {
        return $query->where('is_visible', false);
    }
}
