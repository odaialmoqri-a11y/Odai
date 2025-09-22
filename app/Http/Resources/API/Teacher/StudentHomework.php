<?php

namespace App\Http\Resources\API\Teacher;

use Illuminate\Http\Resources\Json\JsonResource;

class StudentHomework extends JsonResource
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
            //
            'id'                =>  $this->id,
            //'user_name'     =>  $this->student->name,
            'user_fullname'     =>  $this->student->FullName,
            'attachments'       =>  $this->AttachmentPath,
            'submitted_on'      =>  date('d-m-Y',strtotime($this->submitted_on)),
            'checked_on'        =>(($this->checked_on)?date('d-m-Y',strtotime($this->checked_on)):''),
            'checked_by'        =>  $this->teacher->FullName,
            'checked_by_name'   =>  $this->teacher->name,
            'status'            =>  ucfirst($this->status),
            'comments'          =>  $this->comments,
        ];
    }
}