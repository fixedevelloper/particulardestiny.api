<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RoomResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,

            'price' => $this->price,
            'capacity' => $this->capacity,
            'size' => $this->size,

            // ✅ Image principale
            'image' => $this->whenLoaded(
                'featuredImage',
                fn () => new ImageResource($this->featuredImage)
            ),

            // ✅ Galerie
            'images' => ImageResource::collection(
                $this->whenLoaded('images')
            ),

            // ✅ Relations
            'category' => [
                'id' => $this->category?->id,
            'name' => $this->category?->name,
        ],

        'room_type' => [
        'id' => $this->roomType?->id,
            'name' => $this->roomType?->name,
        ],

         'features' => FeatureResource::collection(
        $this->whenLoaded('features')
        ),

        // ✅ COUNT optimisé
        'reservations_count' => $this->whenCounted('reservations'),

        // ✅ Disponibilités safe
        'availabilities' => $this->whenLoaded('availabilities', fn () =>
    $this->availabilities->map(fn($a) => [
        'date' => $a->date,
        'is_available' => $a->is_available,
    ])
    ),

        'created_at' => $this->created_at,
    ];
}
}
