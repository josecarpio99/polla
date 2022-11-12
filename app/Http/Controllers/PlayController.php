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
        return PlayResource::collection(Play::all());
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
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    private function validation($type = null, $request)
    {
        switch ($type) {

            case 'create':

                $validator = [
                    'race_track_id'                  => ['required', 'exists:race_tracks,id'],
                    'start_at'                       => ['required', 'date'],
                    'close_at'                       => ['required', 'date'],
                    'races.*.number'                 => ['required', 'integer'],
                    'races.*.participants_number'    => ['required', 'integer', 'between:5,15'],
                    'prize.*.position'               => ['required', 'integer'],
                    'prize.*.percentage'             => ['required', 'integer']
                ];

                break;

            case 'update':

                $validator = [
                    'name' => ['required', 'string'],
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
