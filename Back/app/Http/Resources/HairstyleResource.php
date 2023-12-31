<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class HairstyleResource extends JsonResource
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
            'price' => $this->price,
            'image' => $this->image ? asset($this->image) : asset('storage/no-image.png'),
            'estimatedTimeMin' => $this->estimated_time_min,
            'hairdresserId' => $this->hairdresser_id,
        ];
    }
}
