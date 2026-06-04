<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'content',
    ];

    public function getContentAttribute($value)
    {
        $decoded = json_decode($value, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return $decoded;
        }
        return $value;
    }

    public function setContentAttribute($value)
    {
        $this->attributes['content'] = is_array($value) ? json_encode($value) : $value;
    }
}
