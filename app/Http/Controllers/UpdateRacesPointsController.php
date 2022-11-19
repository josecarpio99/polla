<?php

namespace App\Http\Controllers;

use App\Http\Resources\TicketResource;
use App\Models\Pick;
use App\Models\Play;
use App\Models\Race;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UpdateRacesPointsController extends Controller
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

        $validator = Validator::make($request->all(), $this->rules());

        if ($validator->fails()) {
            return $this->errorResponse($validator->messages()->first(), 400);
        }

        foreach ($request->input('races') as $race) {
            Race::where('id', $race['id'])->update(['result' => $race['result']]);
            foreach ($race['result'] as $result) {
                Pick::query()
                    ->where('race_id', $race['id'])
                    ->where('picked', $result['number'])
                    ->update(['points' => $result['points']]);
            }
        }
        $tickets = Ticket::where('play_id', $id)->withSum('picks as totalPoints', 'points')->get();

        foreach ($tickets as $ticket) {
            $ticket->points = $ticket->totalPoints;
            $ticket->save();
        }

        return $this->successResponse(null, 'Race result and ticket points updated');
    }

    private function rules()
    {
        return [
            'races.*.id'                => ['required', 'exists:races,id'],
            'races.*.result.*.position' => ['required', 'integer'],
            'races.*.result.*.number'   => ['nullable', 'integer'],
            'races.*.result.*.points'   => ['nullable', 'integer']
        ];
    }
}
