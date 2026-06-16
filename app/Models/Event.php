<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_active',
        'starts_at',
        'ends_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    public function guestMessages()
    {
        return $this->hasMany(GuestMessage::class);
    }

    public function scopeActive(\Illuminate\Database\Eloquent\Builder $query)
    {
        return $query->where('is_active', true);
    }

    public function getFeedbackUrl()
    {
        return route('events.feedback', ['event' => $this->slug]);
    }

    public function getDisplayUrl()
    {
        return route('events.display', ['event' => $this->slug]);
    }
}
