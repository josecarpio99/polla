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

        $perPage = request('per_page', 20);
        $search = request('search', '');
        $sortField = request('sort_field', 'position');
        $sortDirection = request('sort_direction', 'asc');

        $query = Ticket::query()
            ->where('play_id', $play->id)
            ->where(function($query) use($search){
                $query->where('code', 'like', '%' . $search . '%');
            })
            ->orderBy($sortField, $sortDirection)
            ->paginate($perPage);


        return TicketResource::collection($query);
    }
}
