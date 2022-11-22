<?php

namespace App\Http\Controllers;

use App\Models\Play;
use App\Models\User;
use Illuminate\Http\Request;

class SalesAdjustmentController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request, $playId, $userId)
    {
        if (! $play = Play::find($userId)) {
            return $this->notFound();
        }

        if (! $user = User::find($userId)) {
            return $this->notFound();
        }

        $play->loadCount(['tickets as ticketsCount' => function ($query) use($userId) {
            $query->where('user_id', $userId);
        }]);

        $play->loadSum(['tickets as totalSell' => function ($query) use($userId) {
            $query->where('user_id', $userId);
        }], 'price');

        $play->sellerAmount =  number_format($play->totalSell * (config('settings.system_percentage') / 100), 2);

        return response()->json([
            'ticketPrice'   => config('settings.play_cost'),
            'ticketsCount'  => $play->ticketsCount,
            'totalSell'     => $play->totalSell,
            'sellerAmount'  => $play->sellerAmount,
            'deliverToBank' => $play->totalSell - $play->sellerAmount
        ]);
    }
}
