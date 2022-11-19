<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TicketResource extends JsonResource
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
            'id'        => $this->id,
            'code'      => $this->code,
            'points'    => $this->points,
            'play_id'   => $this->play_id,
            'client'    => new ClientResource($this->client),
            'user'      => [
                'id'   => $this->user->id,
                'name' => $this->user->name,
            ],
            'price'     => $this->price,
            'picks'     => PickResource::collection($this->picks),
            'created_at' => $this->created_at,
        ];
    }
}
