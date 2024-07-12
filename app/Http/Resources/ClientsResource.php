<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClientsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'client'=>[
            'company_name'=>$this->company_name,
            'company_owners'=>$this->company_owners,
            'website_link'=>$this->website_link,
            'total_spent'=>$this->total_spent,
            'total_posted_jobs'=>$this->total_posted_jobs
            ],
            'user'=>[
                'username'=>$this->user->username,
                'email'=>$this->user->email,
                'user_id'=>$this->user->id,
                'user_State'=>$this->user->State,
                'isFreezed'=>$this->user->freeze
            ]
        ];
    }
}
