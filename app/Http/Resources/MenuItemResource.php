<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class MenuItemResource extends JsonResource
{
    public function toArray(
        Request $request
    ): array {

        return [
            'id' =>
                $this->id,

            'name' =>
                $this->name,

            'slug' =>
                $this->slug,

            'description' =>
                $this->description,

            'price' =>
                $this->price,

            'is_available' =>
                $this->is_available,

            'image' =>
                $this->image
                    ? Storage::disk('public')
                        ->url(
                            $this->image
                        )
                    : null,

            'category' => $this->whenLoaded(
                'category',
                fn () => [
                    'id' =>
                        $this->category->id,

                    'name' =>
                        $this->category->name,

                    'slug' =>
                        $this->category->slug,
                ]
            ),

            'created_at' =>
                $this->created_at,

            'updated_at' =>
                $this->updated_at,
        ];
    }
}