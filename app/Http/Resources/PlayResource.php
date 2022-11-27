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
            'id'               => $this->id,
            'status'           => $this->status,
            'race_track_id'    => $this->race_track_id,
            'raceTrack'        => $this->raceTrack,
            'prize'            => $this->prize,
            'ticketsCount'     => $this->ticketsCount,
            'totalPrize'       => $this->totalPrize,
            'totalPrizePayout' => $this->totalPrizePayout,
            'races'            => RaceResource::collection($this->races),
            'start_at'         => $this->start_at,
            'close_at'         => $this->close_at,
            'created_at'       => $this->created_at
        ];
    }
}
