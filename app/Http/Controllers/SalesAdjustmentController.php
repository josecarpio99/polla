<?php

namespace App\Http\Controllers;

use App\Models\Play;
use App\Models\User;
use Illuminate\Http\Request;

class SalesAdjustmentController extends Controller
{

    public function index(Request $request, $playId)
    {
        if (! $play = Play::find($playId)) {
            return $this->notFound();
        }

        $role = auth()->user()->role;

        $play->loadCount(['tickets as ticketsCount' => function ($query) use($role) {
            if ($role != 'superadmin') {
                if ($role === 'admin') {
                    $ids = auth()->user()->pos->pluck('id');
                    $ids[] = auth()->user()->id;
                    $query->whereIn('user_id', $ids);
                } elseif($role === 'pos') {
                    $query->where('user_id', auth()->user()->id);
                }
            }
        }]);

        $play->loadSum(['tickets as totalSell' => function ($query) use($role) {
            if ($role != 'superadmin') {
                if ($role === 'admin') {
                    $ids = auth()->user()->pos->pluck('id');
                    $ids[] = auth()->user()->id;
                    $query->whereIn('user_id', $ids);
                } elseif($role === 'pos') {
                    $query->where('user_id', auth()->user()->id);
                }
            }
        }], 'price');

        $play->sellerAmount =  number_format($play->totalSell * (config('settings.system_percentage') / 100), 2);

        return response()->json([
            'ticketPrice'      => config('settings.play_cost'),
            'ticketsCount'     => $play->ticketsCount,
            'totalSell'        => $play->totalSell,
            'sellerAmount'     => $play->sellerAmount,
            'deliverToBank'    => $play->totalSell - $play->sellerAmount,
            'systemPercetange' => config('settings.system_percentage')
        ]);
    }

    public function byUser(Request $request, $playId, $userId)
    {
        if (! $play = Play::find($playId)) {
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
            'ticketPrice'      => config('settings.play_cost'),
            'ticketsCount'     => $play->ticketsCount,
            'totalSell'        => $play->totalSell,
            'sellerAmount'     => $play->sellerAmount,
            'deliverToBank'    => $play->totalSell - $play->sellerAmount,
            'systemPercetange' => config('settings.system_percentage')
        ]);
    }
}
