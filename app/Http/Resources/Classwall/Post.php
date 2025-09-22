<?php

namespace App\Http\Resources\Classwall;

use Illuminate\Http\Resources\Json\JsonResource;

class Post extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        if($this->visibility == 'all_class')
        {
            $visibility = str_replace('_', ' ', ucwords($this->visibility));
        }
        elseif($this->visibility == 'select_class')
        {
            $visibility = $this->StandardLink->StandardSection;
        }
        
        return 
        [
            //
            'id'                =>  $this->id,
            'description'       =>  $this->description,
            'post_created_at'   =>  $this->post_created_at->diffForHumans(),
            'attachments'       =>  $this->AttachmentPath,
            'visibility'        =>  $visibility,
            'created_by'        =>  $this->created_by,
        ];
    }
}