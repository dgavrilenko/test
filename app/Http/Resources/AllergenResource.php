<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AllergenResource extends JsonResource
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
            'name' => $this->name,
            'type' => $this->type,
            'category' => $this->category,
            'category_id' => $this->category_id,
            'type_id' => $this->type_id,
            'code' => $this->code,
            'description' => $this->description,
            'composition' => $this->composition,
            'quantity' => 0,
        ];
    }
}
