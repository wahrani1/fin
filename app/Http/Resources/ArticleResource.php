<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ArticleResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'category' => $this->category,
            'era' => $this->whenLoaded('era'),
            'governorate' => $this->whenLoaded('governorate'),
            'user' => $this->whenLoaded('user'),
            'images' => $this->whenLoaded('images'),
            'comments' => $this->whenLoaded('comments'),
            'ratings' => $this->whenLoaded('ratings'),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
