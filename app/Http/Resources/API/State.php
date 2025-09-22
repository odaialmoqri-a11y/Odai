<?php

namespace App\Http\Resources\API;

use Illuminate\Http\Resources\Json\JsonResource;

class State extends JsonResource
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
           'countryId' => $this->country_id,
           'name'       => $this->name,
           'status'     => $this->status,
       ];
    }
}
