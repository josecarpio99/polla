<?php

namespace App\Http\Resources;

use App\Models\Ticket;
use Illuminate\Http\Resources\Json\JsonResource;

class TicketRankingResource extends JsonResource
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
            'id'         => $this->id,
            'code'       => $this->code,
            'points'     => $this->points,
            'play_id'    => $this->play_id,
            'price'      => $this->price,
            'picks'      => PickResource::collection($this->picks),
            'created_at' => $this->created_at,
        ];
    }
}
