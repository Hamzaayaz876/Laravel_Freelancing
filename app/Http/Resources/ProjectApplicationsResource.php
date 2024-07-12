<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectApplicationsResource extends JsonResource
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
            'Cover_Letter' => $this->Cover_Letter,
           'Project_Title' => $this->project->Title,
           'Project_id' => $this->project->id,
            'Freelancer' => new FreelancersResource($this->freelancer),
        ];
    }
}
