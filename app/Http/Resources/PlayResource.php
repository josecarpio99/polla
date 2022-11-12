<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PlayResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'          => $this->id,
            'raceTrack'   => $this->raceTrack->name,
            'prize'       => $this->prize,
            'races'       => RaceResource::collection($this->races),
            'start_at'    => $this->start_at,
            'close_at'    => $this->close_at,
            'created_at'  => $this->created_at,
        ];
    }
}
