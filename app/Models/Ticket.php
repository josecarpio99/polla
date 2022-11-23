<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{

    const WINNER  = 1;
    const LOSER   = 2;
    const PENDING = 3;

    public static function getWinners($playId, $positions = 2)
    {
        $winners = [];
        $topScorers = Ticket::where('play_id', $playId)
            ->groupBy('points')
            ->orderBy('points', 'DESC')
            ->take($positions)
            ->pluck('points')
            ->toArray();

        foreach ($topScorers as $key => $score) {
            $winners[] = [
                'position' => $key + 1,
                'ids'      => Ticket::where('play_id', $playId)
                    ->where('points', '>', 0)
                    ->where('points', $score)
                    ->pluck('id')
                    ->toArray()
            ];
        }
        return $winners;
    }

    public function scopeSearch($query, $search)
    {
        return $query
            ->where('code', 'like', '%' . $search . '%');
    }

    public function picks()
    {
        return $this->hasMany(Pick::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function booted()
    {
        static::creating(function($ticket) {
            $lastTicket = Ticket::orderBy('code', 'DESC')->first();
            $code = ($lastTicket) ? $code = (ltrim($lastTicket->code, '0')) + 1 : 1;
            $ticket->code = str_pad($code, 6, '0', STR_PAD_LEFT);
        });
    }
}
