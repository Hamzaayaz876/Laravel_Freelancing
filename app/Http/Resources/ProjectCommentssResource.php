<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectCommentssResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
            return [
                'Comment' => [
                    'Text' => $this->Text,
                    'State' => $this->State,
                    'user_info' => [
                        'Freelancer_id' => $this->freelancer->id,
                        'picture' => base64_encode($this->freelancer->picture),
                        'first_name' => $this->freelancer->firstname,
                        'last_name' => $this->freelancer->lastname,
                        'skill_name' => $this->freelancer->skill_name,
                        'username' => $this->freelancer->user->username,
                        'user_id' => $this->freelancer->user->id

                    ]
                ]
            ];
        }
}
