<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
            'title'=>ucfirst($this->title),
            'price'=>$this->price,
            'description'=>$this->description,
            'image'=>$this->image?asset('images/' . $this->image):null
        ];
    }
}
