<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pick extends Model
{
    public $timestamps = false;

    public function race()
    {
        return $this->belongsTo(Race::class);
    }
}
