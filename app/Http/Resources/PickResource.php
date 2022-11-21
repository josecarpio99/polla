<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PickResource extends JsonResource
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
            'id'           => $this->id,
            'ticket_id'    => $this->ticket_id,
            'picked'       => $this->picked,
            'next_pick'    => $this->next_pick,
            'points'       => $this->points,
            'race'         => new RaceResource($this->race)
        ];
    }
}
