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
            'id' => $this->id,
            'number' => $this->number,
            'active' => $this->active,
            'name' => $this->name,
            'header' => $this->header,
            'type_id' => $this->type_id,
            'category_id' => $this->category_id,
            'description' => $this->description,
            'price' => $this->price,
            'units' => $this->units,
            'multiplicity' => $this->multiplicity,
            'definitions_number' => $this->definitions_number,
            'quantity' => 0,
        ];
    }
}
