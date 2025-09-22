<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TeacherClasses extends JsonResource
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
            //
            'standard'  =>  $this->standardLink->StandardName,
            'section'   =>  $this->standardLink->section->name,
            'subject'   =>  ucwords($this->subject->name),
        ];
    }
}
