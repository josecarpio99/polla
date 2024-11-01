<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'id'       =>  $this->id,
            'name'     =>  $this->name,
            'username' =>  $this->username,
            'email'    =>  $this->email,
            'role'     =>  $this->role,
            'phone'    =>  $this->phone,
            'address'  =>  $this->address,
            'pos'      =>  UserResource::collection($this->pos)
        ];
    }
}
