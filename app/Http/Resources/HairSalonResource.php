<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class HairSalonResource extends JsonResource
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
            'id' => $this->id,
            'name' => $this->name,
            'cityId' => $this->city_id,
            'address' => $this->address,
            'description' => $this->description,
            'managerId' => $this->manager_id
        ];
    }
}
