<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'username' => $this->username,
            $this->mergeWhen(auth()->id() === $this->id, [
                'email' => $this->email,
            ]),
            $this->mergeWhen(request()->has(['latitude', 'longitude', 'distance']), [
                'distance' => $this->calcDistanceBetweenUsers(request()->unit)
            ]),
            'name' => $this->name,
            'photo_url' => $this->photo_url,
            'create_dates' => [
                'created_at_human' => $this->created_at->diffForHumans(),
                'created_at' => $this->created_at,
            ],
            'designs' => DesignResource::collection($this->whenLoaded('designs')),
            'formatted_address' => $this->formatted_address,
            'tagline' => $this->tagline,
            'about' => $this->about,
            'location' => $this->location,
            'available_to_hire' => $this->available_to_hire,
        ];
    }
}
