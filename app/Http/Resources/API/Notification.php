<?php

namespace App\Http\Resources\API;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class Notification extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $data = json_decode($this->data);
        $datetime = date('d-m-Y H:i:s',strtotime($this->created_at));
        return [
            //
            'id'                =>  $this->id,
            'type'              =>  $this->type,
            'notifiable_type'   =>  $this->notifiable_type,
            'notifiable_id'     =>  $this->notifiable_id,
            'data_message'      =>  $data->data,
            'read_at'           =>  $this->read_at == null ? null:Carbon::parse($this->read_at)->diffForHumans(),
            'created_at'        =>  Carbon::parse($this->created_at)->diffForHumans(),
            'datetime'=>$datetime,
        ];
    }
}
