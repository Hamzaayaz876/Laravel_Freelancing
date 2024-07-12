<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class reportFreelancerResource extends JsonResource
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
                'Report_id'=>$this->id,
                'Report_type' => $this->Report_type,
                'text' => $this->text,
                'Reported_freelancer' => [
                    'Freelancer_id' => $this->freelancer->id,
                    'picture' => base64_encode($this->freelancer->picture),
                    'first_name' => $this->freelancer->firstname,
                    'last_name' => $this->freelancer->lastname,
                    'skill_name' => $this->freelancer->skill_name,
                    'username' => $this->freelancer->user->username,
                    'user_id' => $this->freelancer->user->id
                ],
                'The_one_who_reports'=>$this->user_id
            ]
        ];
    }
}
