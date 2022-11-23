<?php

namespace App\Http\Controllers;

use App\Models\Play;
use App\Models\Ticket;
use Illuminate\Http\Request;

class UpdateTicketsStatusController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request, $playId)
    {
        if (! $play = Play::find($playId)) {
            return $this->notFound();
        }

        $play->loadCount('tickets as ticketsCount');

        $play->loadSum('tickets as totalPrize', 'price');

        $play->prize = $play->prize->map(function($prize, $key) use($play) {
            $prize['total']   = $play->totalPrize * ($prize['percentage'] / 100);
            $prize['winners'] = $play->tickets()->where('position', $prize['position'])->count();
            $prize['earned']  = $prize['winners'] ? $prize['total'] / $prize['winners'] : $prize['total'];
            return $prize;
        });

        $play->prize->each(function($prize) use($play) {
            $play->tickets()
                ->where('position', $prize['position'])
                ->update([
                    'status' => Ticket::WINNER,
                    'earned' => $prize['earned'],
                ]);
        });

        $positionsRewarded = $play->prize->pluck('position');

        $play->tickets()
            ->whereNotIn('position', $positionsRewarded)
            ->update([
                'status' => Ticket::LOSER,
                'earned' => 0
            ]);

        return $this->successResponse(null, 'Tickets status updated');
    }
}
