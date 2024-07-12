<?php

namespace App\Http\Resources;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClientConversationResourse extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        return [
            'Conversation' => [
                'Coneversation_ID'=>$this->id,
                'State' => $this->State,
                'ProjectTitle' => $this->project->Title,

                'Freelancer_info' => [
                    'Freelancer_id' => $this->freelancer->id,
                    'first_name' => $this->freelancer->firstname,
                    'last_name' => $this->freelancer->lastname,
                    'username' => $this->freelancer->user->username
                ]
            ]
        ];
    }
    }

