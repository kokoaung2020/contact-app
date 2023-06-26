<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use App\Http\Resources\GalleryResource;
use Illuminate\Http\Resources\Json\JsonResource;

class ContactResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'phone' => $this->phone,
            'email' => $this->email,
            'address' => $this->address,
            "date" => $this->created_at->format("d M Y"),
            "time" => $this->created_at->format("H:i"),
            'owner' => $this->user->name,
            'galleries' => GalleryResource::collection($this->galleries)
        ];
    }
}
