<?php

namespace App\Http\Controllers;

use App\Models\Play;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Resources\PlayResource;
use Illuminate\Support\Facades\Validator;

class PlayController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $perPage = request('per_page', 10);
        $search = request('search', '');
        $sortField = request('sort_field', 'updated_at');
        $sortDirection = request('sort_direction', 'desc');

        $query = Play::query()
            ->search($search)
            ->orderBy($sortField, $sortDirection)
            ->paginate($perPage);


        return PlayResource::collection($query);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = $this->validation('create', $request);

        if ($validator->fails()) {
            return $this->errorResponse($validator->messages()->first(), 400);
        }

        $data = $validator->validated();

        $play = Play::create([
            'race_track_id' => $data['race_track_id'],
            'prize'         => $data['prize'],
            'start_at'      => $data['start_at'],
            'close_at'      => $data['close_at'],
        ]);

        foreach ($data['races'] as $race) {
            $play->races()->create($race);
        }

        return $this->createdResponse(new PlayResource($play));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (! $play = Play::find($id)) {
            return $this->notFound();
        }
        return response()->json(new PlayResource($play));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (! $play = Play::find($id)) {
            return $this->notFound();
        }
        $validator = $this->validation('update', $request);

        if ($validator->fails()) {
            return $this->errorResponse($validator->messages()->first(), 400);
        }

        $data = $validator->validated();

        $play->update([
            'race_track_id' => $data['race_track_id'],
            'prize'         => $data['prize'],
            'start_at'      => $data['start_at'],
            'close_at'      => $data['close_at'],
        ]);

        $playRacesIds = $play->races()->pluck('id')->toArray();
        $raceIds = collect($data['races'])->pluck('id')->toArray();
        if (array_diff($playRacesIds, $raceIds)) {
            if ($play->tickets()->exists()) {
                return $this->errorResponse('No puedes remover las carrera/s, ya existen tickets creados', 400);
            }
        }

        $play->races()->createUpdateOrDelete($data['races']);

        return $this->successResponse(new PlayResource($play));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (! $play = Play::find($id)) {
            return $this->notFound();
        }

        $play->delete();

        return $this->noContentResponse();
    }

    private function validation($type = null, $request)
    {
        switch ($type) {

            case 'create':

                $validator = [
                    'race_track_id'                  => ['required', 'exists:race_tracks,id'],
                    'start_at'                       => ['required', 'date'],
                    'close_at'                       => ['required', 'date'],
                    'status'                         => ['nullable', 'boolean'],
                    'races.*.number'                 => ['required', 'integer'],
                    'races.*.participants_number'    => ['required', 'integer', 'between:5,15'],
                    'races.*.removed'                => ['nullable', 'regex:/^\d(?:,\d)*$/'],
                    'prize.*.position'               => ['required', 'integer'],
                    'prize.*.percentage'             => ['required', 'integer']
                ];

                break;

            case 'update':

                $validator = [
                    'race_track_id'                  => ['required', 'exists:race_tracks,id'],
                    'start_at'                       => ['required', 'date'],
                    'close_at'                       => ['required', 'date'],
                    'status'                         => ['nullable', 'boolean'],
                    'races.*.id'                     => ['nullable', 'exists:races,id'],
                    'races.*.number'                 => ['required', 'integer'],
                    'races.*.participants_number'    => ['required', 'integer', 'between:5,15'],
                    'races.*.removed'                => ['nullable', 'regex:/^\d(?:,\d)*$/'],
                    'prize.*.position'               => ['required', 'integer'],
                    'prize.*.percentage'             => ['required', 'integer']
                ];

                break;

            default:

                $validator = [];
        }

        $validator = Validator::make($request->all(), $validator);

        if ($validator->fails()) {
            return $validator;
        }

        $validator->after(function ($validator) {

            $afterValidator = Validator::make(['prize' => $validator->validated()['prize']], [
                'prize'  => [
                        function ($attribute, $value, $fail) {
                            $values = collect($value);

                            if ($values->sum('percentage') > 100) {
                                $fail('La suma de porcentaje del premio no puede ser mayor a 100');
                            }

                            $sorted = $values->sortBy('position');
                            $currentPosition = 1;

                            $sorted->each(function ($item, $key) use(&$currentPosition, $fail) {
                                if ($item['position'] !== $currentPosition) {
                                    $fail('La posición de los premios no es válida');
                                }
                                $currentPosition++;
                            });
                        },
                    ],
                ]);

            if ($afterValidator->fails()) {
                $validator->errors()->add('prize', $afterValidator->messages()->first());
            }
        });

        return $validator;
    }
}
