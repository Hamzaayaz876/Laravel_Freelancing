<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class privateComments extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request)
    {

        return [
            'Comment'  =>[
            'Text'=>[
            'Text'=>$this->Text,
            ],
            'State'=>$this->State,
            'user_info'=>[
                'Freelancer_id'=>$this->freelancer->id,
                'picture'=>base64_encode($this->freelancer->picture),
                'first_name'=>$this->freelancer->firstname,
               'last_name'=>$this->freelancer->lastname,
                'skill_name'=>$this->freelancer->skill_name,
                'username'=>$this->freelancer->user->username
            ]]
        ];
    }
}
