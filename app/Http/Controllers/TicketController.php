<?php

namespace App\Http\Controllers;

use App\Models\Play;
use App\Models\Client;
use App\Models\Ticket;
use Illuminate\Http\Request;
use App\Http\Resources\TicketResource;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class TicketController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($playId = null)
    {
        if (! $play = Play::find($playId)) {
            return $this->notFound();
        }

        $perPage = request('per_page', 10);
        $search = request('search', '');
        $sortField = request('sort_field', 'created_at');
        $sortDirection = request('sort_direction', 'desc');
        $role = auth()->user()->role;

        $query = Ticket::query()
            ->with(['client'])
            ->where('play_id', $play->id)
            ->when($role !== 'superadmin', function($query) use($role) {
                if ($role === 'admin') {
                    $query->whereIn('user_id', auth()->user()->pos->pluck('id')->toArray());
                } elseif($role === 'pos') {
                    $query->where('user_id', auth()->user()->id);
                }
            })
            ->where(function($query) use($search){
                $query->where('code', 'like', '%' . $search . '%')
                    ->orWhereHas('client', function($query) use ($search){
                        $query->where('id_card', 'like', '%'.$search.'%')
                            ->orWhere('name', 'like', '%'.$search.'%');
                    });
            })
            ->orderBy($sortField, $sortDirection)
            ->paginate($perPage);


        return TicketResource::collection($query);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $playId)
    {
        if (! $play = Play::find($playId)) {
            return $this->notFound();
        }

        if ($play->status == false || $play->close_at < Carbon::now()) {
            return $this->errorResponse('No se puede crear el ticket, la jugada ha cerrado', 400);
        }

        $validator = $this->validation('create', $request);

        if ($validator->fails()) {
            return $this->errorResponse($validator->messages()->first(), 400);
        }

        $data = $validator->validated();

        $client = Client::updateOrCreate(
            ['id_card' => $data['client']['id_card']],
            ['name' => $data['client']['name']]
        );

        $ticket = Ticket::create([
            'play_id'   => $playId,
            'client_id' => $client->id,
            'user_id'   => auth()->user()->id,
            'price'     => config('settings.play_cost'),
        ]);

        foreach ($data['picks'] as $pick) {
            $ticket->picks()->create($pick);
        }

        return $this->createdResponse(new TicketResource($ticket));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($playId, $id)
    {
        if (! $play = Play::find($playId)) {
            return $this->notFound();
        }

        if (! $ticket = Ticket::find($id)) {
            return $this->notFound();
        }

        return response()->json(new TicketResource($ticket));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $playId, $id)
    {
        if (! $play = Play::find($playId)) {
            return $this->notFound();
        }

        if (! $ticket = Ticket::find($id)) {
            return $this->notFound();
        }

        $validator = $this->validation('update', $request);

        if ($validator->fails()) {
            return $this->errorResponse($validator->messages()->first(), 400);
        }

        $data = $validator->validated();

        $client = Client::updateOrCreate(
            ['id_card' => $data['client']['id_card']],
            ['name' => $data['client']['name']]
        );

        $ticket->update([
            'client_id' => $client->id
        ]);

        foreach ($data['picks'] as $pick) {
            $ticket->picks()->where('id', $pick['id'])->update(['picked' => $pick['picked']]);
        }

        return $this->successResponse(new TicketResource($ticket));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($playId, $id)
    {
        if (! $play = Play::find($playId)) {
            return $this->notFound();
        }

        if (! $ticket = Ticket::find($id)) {
            return $this->notFound();
        }

        $ticket->delete();

        return $this->noContentResponse();
    }

    private function validation($type = null, $request)
    {
        switch ($type) {

            case 'create':

                $validator = [
                    'client.id_card'  => ['required', 'integer'],
                    'client.name'     => ['required', 'string'],
                    'picks.*.race_id' => ['required', 'exists:races,id'],
                    'picks.*.picked'  => ['required', 'integer'],
                ];

                break;

            case 'update':

                $validator = [
                    'client.id_card'  => ['required', 'integer'],
                    'client.name'     => ['required', 'string'],
                    'picks.*.id'      => ['required', 'exists:picks,id'],
                    'picks.*.picked'  => ['required', 'integer'],
                ];

                break;

            default:

                $validator = [];
        }

        return Validator::make($request->all(), $validator);
    }
}
