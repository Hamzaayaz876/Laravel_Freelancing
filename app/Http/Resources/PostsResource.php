<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'=>$this->id,
            'attributes'=>[
                'title'=>$this->title,
                'description'=>$this->description,
                'required'=>$this->required
            ],
            'relationships'=>[
                'name'=>$this->user->name
            ]
        ];
    }
}