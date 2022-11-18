<?php

namespace App\Http\Controllers;

use App\Models\Play;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\TicketResource;

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

        $perPage = request('per_page', 20);
        $search = request('search', '');
        $sortField = request('sort_field', 'close_at');
        $sortDirection = request('sort_direction', 'desc');

        DB::select("set @maxPoints = (SELECT points FROM tickets ORDER BY points DESC LIMIT 1);");
        DB::select("set @curRank = 1;");

        $query = Ticket::query()
            ->select(
                '*',
                DB::raw("@curRank := IF(points = @maxPoints, @curRank, @curRank + 1) AS rank"),
                DB::raw("@maxPoints := IF(points = @maxPoints, @maxPoints, points) as maxPoints"),
            )
            ->orderBy('points', 'DESC')
            ->paginate($perPage);

        return TicketResource::collection($query);
    }
}
