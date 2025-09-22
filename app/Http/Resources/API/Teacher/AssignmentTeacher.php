<?php

namespace App\Http\Resources\API\Teacher;

use Illuminate\Http\Resources\Json\JsonResource;

class AssignmentTeacher extends JsonResource
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
            'standardLink_id'   =>  $this->standardLink_id,
            'standard_section'  =>  $this->standardLink->StandardSection,
        ];
    }
}