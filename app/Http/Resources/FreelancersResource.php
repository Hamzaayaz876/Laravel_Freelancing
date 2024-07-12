<?php

namespace App\Http\Resources;

use App\Models\Freelancer_tags;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


class FreelancersResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array

    {
        return [
            'freelancer'=>[
            'freelancer_id'=>$this->id,
            'firstname'=>$this->firstname,
            'lastname'=>$this->lastname,
            'skill_name'=>$this->skill_name,
            'bio'=>$this->bio,
            'cv' => base64_encode($this->cv),
            'picture' => base64_encode($this->picture),
            'Total_Rated_times'=>$this->Total_Rated_times,
            'total_rating'=>$this->Total_Rating,
            'total_submitted_jobs'=>$this->total_compeleted_jobs,
            'Category'=>$this->Category

        ],
            'user'=>[
                'username'=>$this->user->username,
                'email'=>$this->user->email,
                'user_id'=>$this->user->id,
                'user_State'=>$this->user->State,
                'isFreezed'=>$this->user->freeze

            ],
            'Tags'=>[
                'tags'=>$this->tags->pluck('Tag_name')
            ]
        ];

    }
}
//knowing that the picture is of image type and cv is a pdf file and a freelancer can have more than one tag
