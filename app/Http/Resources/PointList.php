<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PointList extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id'=>$this->id,
            'title'=>$this->title,
            'amount'=>$this->amount,
            'point'=>$this->point,
            'category_id'=>$this->category_id,
            'image'=>getPointImageUrl($this->image),
            'discount_rate'=>isset($this->discount_rate) ? $this->discount_rate : 0,
        ];
    }
}
