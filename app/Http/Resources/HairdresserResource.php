<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class HairdresserResource extends JsonResource
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
            'name' => $this->user->name,
            'surname' => $this->user->surname,
            'phoneNr' => $this->phone_nr,
            'isApproved' => $this->is_approved,
            'hairSalonId' => $this->hair_salon_id
        ];
    }
}
