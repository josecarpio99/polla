<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Play extends Model
{
    protected $casts = [
        'prize' => 'collection'
    ];

    public function scopeSearch($query, $search)
    {
        return $query
            ->where('start_at', 'like', '%' . $search . '%')
            ->orWhere('close_at', 'like', '%' . $search . '%');
    }

    public function raceTrack()
    {
        return $this->belongsTo(RaceTrack::class);
    }

    public function races()
    {
        return $this->hasMany(Race::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }
}
