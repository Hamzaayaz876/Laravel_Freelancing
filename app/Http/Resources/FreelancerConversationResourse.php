<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FreelancerConversationResourse extends JsonResource
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

                'Client_info' => [
                    'Client_id' => $this->Client->id,
                    'company_name' => $this->Client->company_name,
                    'username' => $this->Client->user->username
                ]
            ]
        ];
    }
}
