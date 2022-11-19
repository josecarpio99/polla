<?php

namespace App\Http\Controllers;

use App\Models\Play;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\TicketResource;
use App\Http\Resources\TicketRankingResource;

class RankingTicketsController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request, $id)
    {
        if (! $play = Play::find($id)) {
            return $this->notFound();
        }

        // $perPage = request('per_page', 20);
        // $search = request('search', '');

        // DB::select("set @maxPoints = (SELECT points FROM tickets WHERE play_id = $id ORDER BY points DESC LIMIT 1);");
        // DB::select("set @curRank = 1;");

        // $query = Ticket::query()
        //     ->select(
        //         '*',
        //         DB::raw("@curRank := IF(points = @maxPoints, @curRank, @curRank + 1) AS rank"),
        //         DB::raw("@maxPoints := IF(points = @maxPoints, @maxPoints, points) as maxPoints"),
        //     )
        //     ->where('play_id', $id)
        //     ->orderBy('points', 'DESC')
        //     ->paginate($perPage);

        $perPage = request('per_page', 20);
        $search = request('search', '');
        $sortField = request('sort_field', 'points');
        $sortDirection = request('sort_direction', 'desc');

        $query = Ticket::query()
            ->where('play_id', $play->id)
            ->where(function($query) use($search){
                $query->where('code', 'like', '%' . $search . '%');
            })
            ->orderBy($sortField, $sortDirection)
            ->paginate($perPage);


        return TicketRankingResource::collection($query)->additional(['meta' => [
            'winners' => Ticket::getWinners($id),
        ]]);
    }
}
