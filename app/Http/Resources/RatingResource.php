<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RatingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'Report' => [
                'Rating_id'=>$this->id,
                'Review' => $this->Review,
                'Rating' => $this->number,
                'Client' => $this->client->id,
                'Company_name' => $this->client->company_name,
                'Project_Title' => $this->project->Title,
            ]
        ];
    }
}
