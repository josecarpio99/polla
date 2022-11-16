<?php

namespace App\Http\Controllers;

use App\Models\Play;
use Illuminate\Http\Request;

class TogglePlayStatusController extends Controller
{
    public function __invoke($id)
    {
        if (! $play = Play::find($id)) {
            return $this->notFound();
        }

        $play->status = !$play->status;
        $play->save();

        return $this->successResponse(null, 'Play status updated');
    }
}
