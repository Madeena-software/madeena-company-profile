<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GuestMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'name',
        'organization',
        'position',
        'phone',
        'email',
        'kesan_dan_pesan',
        'is_visible',
    ];

    protected $casts = [
        'is_visible' => 'boolean',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function scopeVisible(\Illuminate\Database\Eloquent\Builder $query)
    {
        return $query->where('is_visible', true);
    }

    public function scopeHidden(\Illuminate\Database\Eloquent\Builder $query)
    {
        return $query->where('is_visible', false);
    }
}
