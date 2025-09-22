<?php

namespace App\Http\Resources\Payroll;

use App\Http\Resources\Payroll\TemplateItemResource;
use Illuminate\Http\Resources\Json\JsonResource;

class TemplateResource extends JsonResource
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
            'id'=>$this->id,
            'name'=>$this->name,
            'created_by'=>$this->user->FullName,
            'status'=>$this->status,
            'created_at'=>$this->created_at->format('Y-m-d H:m:s'),
             'updated_at'=>$this->updated_at->format('Y-m-d H:m:s'),
             'payrollitems' => TemplateItemResource::collection($this->payrollitems)

        
        ];
    }
}
