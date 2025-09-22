<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Traits\Common;

class ShowVideo extends JsonResource
{
    use Common;

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        if($this->media_type == 'url')
        {
            $url = $this->url;
        }
        else
        {
            $url = $this->AttachmentPath;         
        }

        if($this->type=='image')
        {
            $thumb = $this->AttachmentPath;
        }
        elseif($this->type=='audio')
        {
             $thumb = $this->getFilePath('uploads/audio.png');
        }
        else
        {
             $thumb = $this->getFilePath('uploads/video.png');
        }
       
        return [
            'id'            =>  $this->id,
            'standard'      =>  $this->standardLink->StandardSection,
            'media'         =>  ucwords(str_replace('_', ' ', $this->media)),        
            'name'          =>  str_limit($this->name,20,'....'),        
            'description'   =>  str_limit($this->description,20,'....'),
            'type'          =>  $this->type,
            'media_type'    =>  $this->media_type,
            'url'           =>  $url,
            'thumb_file'    =>  $thumb,
            'downloadurl'   =>  $this->getFilePath('videos/download/'.$this->id),
            'viewers'       =>  count($this->viewers),
        ];
    }
}