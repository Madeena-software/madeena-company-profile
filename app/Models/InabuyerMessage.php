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
    ];
}
