<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Homework extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        if($this->standardLink_id != null)
        {
            $class_name = $this->standardLink->StandardSection;
        }
        else
        {
            $class_name = '--';
        }

        if($this->attachment != null)
        {
            $attachment = $this->AttachmentPath;
            $extension=pathinfo( $attachment, PATHINFO_EXTENSION);//dd($extension);
            if(in_array($extension,['jpg','jpeg','png']))
            {
              $type='image';
            }
            elseif(in_array($extension,['mp3']))
            {
                $type='audio';
            }
            elseif(in_array($extension,['mp4']))
            {
                $type='video';
            }
            elseif(in_array($extension,['pdf']))
            {
                $type='pdf';
            }
            else
            {
                 $type='';
            }
        }
        else
        {
            $attachment = '';
            $type='';
        }

        return 
        [
            'id'                =>  $this->id,
            'class_name'        =>  $class_name,
            'subject_name'      =>  $this->subject->name,
            'date'              =>  date('d-m-Y', strtotime($this->date)),
            'description'       =>  $this->description,
            'attachment'        =>  $attachment,
            'pending_count'     =>  $this->PendingCount,
            'finished_count'    =>  $this->FinishedCount,
            'status_display'    =>  ucwords($this->homeworkApproval->status),
            'status'            =>  $this->homeworkApproval->status,
            'comments'          =>  $this->homeworkApproval->comments ==  null ? '--':$this->homeworkApproval->comments,
            'auth_id'           =>  \Auth::id(),
            'created_by'        =>  $this->created_by,
            'type'              =>  $type,
            'submission_date'   =>  $this->submission_date==null?'':date('d-m-Y', strtotime($this->submission_date)),
        ];
    }
}