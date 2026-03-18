<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class CategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Build full image URL if image exists
        $imageUrl = null;
        if ($this->image) {
            // If image is already a full URL, use it
            if (str_starts_with($this->image, 'http')) {
                $imageUrl = $this->image;
            }
            // If image is a storage path, generate URL
            elseif (Storage::disk('public')->exists($this->image)) {
                $imageUrl = Storage::disk('public')->url($this->image);
                $imageUrl = (str_starts_with($imageUrl, 'http')) ? $imageUrl : asset($imageUrl);
            }
            // If image is just a filename in categories directory
            elseif (Storage::disk('public')->exists('categories/'.$this->image)) {
                $imageUrl = Storage::disk('public')->url('categories/'.$this->image);
                $imageUrl = (str_starts_with($imageUrl, 'http')) ? $imageUrl : asset($imageUrl);
            }
            // Fallback: assume it's a path relative to storage/public
            else {
                $imageUrl = asset('storage/'.ltrim($this->image, '/'));
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
