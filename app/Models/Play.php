<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Play extends Model
{
    protected $casts = [
        'prize' => 'array'
    ];

    public function raceTrack()
    {
        return $this->belongsTo(RaceTrack::class);
    }
}
