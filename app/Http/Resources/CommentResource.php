<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
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
            'topic' => $this->topic,
            'content' => $this->content,
            'is_reply' => $this->parent_id !== null,
            'parent_id' => $this->parent_id,

            // User information
            'user' => $this->whenLoaded('user', function () {
                return [
                    'id' => $this->user->id,
                    'name' => $this->user->name,
                    'email' => $this->user->email,
                ];
            }),

            // Parent comment (if this is a reply)
            'parent' => $this->whenLoaded('parent', function () {
                return [
                    'id' => $this->parent->id,
                    'topic' => $this->parent->topic,
                    'content' => $this->parent->content,
                    'user' => [
                        'id' => $this->parent->user->id,
                        'name' => $this->parent->user->name,
                    ],
                ];
            }),

            // Nested replies
            'replies' => $this->whenLoaded('nestedReplies', function () {
                return CommentResource::collection($this->nestedReplies);
            }),

            // Reply count
            'reply_count' => $this->when(
                ! $this->relationLoaded('nestedReplies'),
                fn () => $this->replies()->count()
            ),

            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
            'edited_at' => $this->updated_at && $this->updated_at->gt($this->created_at)
                ? $this->updated_at->toIso8601String()
                : null,
        ];
    }
}
