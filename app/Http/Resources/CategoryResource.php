<?php

namespace App\Http\Resources;

use App\Services\FileStorageService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $fileStorage = app(FileStorageService::class);

        $imageUrl = null;
        if ($this->image) {
            if (str_starts_with($this->image, 'http')) {
                $imageUrl = $this->image;
            } elseif ($fileStorage->exists($this->image)) {
                $imageUrl = $fileStorage->url($this->image);
            } elseif ($fileStorage->exists('categories/'.$this->image)) {
                $imageUrl = $fileStorage->url('categories/'.$this->image);
            } else {
                $imageUrl = $fileStorage->url($this->image);
            }
        }

        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'icon' => $this->image,
            'color' => $this->color,
            'is_active' => $this->is_active,
            'sort_order' => $this->sort_order,
            'created_at' => formatDateTime($this->created_at),
            'updated_at' => formatDateTime($this->updated_at),
        ];
    }
}
