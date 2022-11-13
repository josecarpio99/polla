<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    public static function booted()
    {
        static::creating(function($ticket) {
            $ticket->code = str_pad(Ticket::count() + 1, 6, '0', STR_PAD_LEFT);
        });
    }
}
