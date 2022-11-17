<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{

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
            $code = (ltrim($lastTicket->code, '0')) + 1;
            $ticket->code = str_pad($code, 6, '0', STR_PAD_LEFT);
        });
    }
}
