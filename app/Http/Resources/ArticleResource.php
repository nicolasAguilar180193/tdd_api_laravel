<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ArticleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'type' => 'articles',
            'id' => (string) $this->getRouteKey(),
            'attributes' => [
                'title' => $this->title,
                'slug' => $this->slug,
                'content' => $this->content
            ],
            'links' => [
                'self' => route('api.v1.articles.show', $this->resource)
            ]
        ];
    }

    public function toResponse($request)
    {
        return parent::toResponse($request)->withHeaders([
            'Location' => route('api.v1.articles.show', $this->resource)
        ]);
    }
}
