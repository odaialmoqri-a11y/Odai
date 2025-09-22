<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Studentlist extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return 
        [
            'standardLink_id'   =>  $this->studentAcademicLatest->standardLink_id,
            'class'             =>  $this->studentAcademicLatest->standardLink->StandardSection,
            'user_id'           =>  $this->id,
            'id'                =>  $this->id,
            'name'              =>  $this->FullName,
            'avatar'            =>  optional($this->userprofile)->AvatarPath,
        ];
    }
}