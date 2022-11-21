<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Race extends Model
{
    protected $casts = [
        'result' => 'array'
    ];

    public function getRemoved() : array
    {
        return explode(',', $this->removed);
    }

    public function getNextPick($removed) : int
    {
        $removed++;
        while ($removed > $this->participants_number || $this->isRemoved($removed)) {
            if ($removed > $this->participants_number) $removed = 1;
            if ($this->isRemoved($removed)) $removed++;
        }
        return $removed;
    }

    public function isRemoved($number) : bool
    {
        return in_array($number, $this->getRemoved());
    }

    public function updateNextPickForRemoved () : void
    {
        $numbersRemoved = $this->getRemoved();
        if (empty($numbersRemoved)) return;
        foreach ($numbersRemoved as $key => $removed) {
            $nextPick = $this->getNextPick($removed);
            Pick::where('race_id', $this->id)->where('picked', $removed)->update(['next_pick' => $nextPick]);
        }
    }
}
