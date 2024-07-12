<?php

namespace App\Http\Resources;

use App\Models\project_tags;
use App\Models\ProjectComments;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class ProjectsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
{


    return [
        'Project' => [
            'id'=>$this->id,
            'Title' => $this->Title,
            'Description' => $this->Description,
            'skill_name' => $this->skill_name,
            'Level' => $this->Level,
            'Applications_Number' => $this->Applications_Number,
            'State' => $this->State,
            'Budget' => $this->Budget,
            'Category' => $this->Category,
            'Application_Dealine' => $this->Application_Dealine,
            'Comments_Number' => $this->Comments_Number
        ],
        'Client' => [
            'company_name' => $this->Client->company_name,
            'company_owners' => $this->Client->company_owners,
            'website_link' => $this->Client->website_link,
            'total_spent' => $this->Client->total_spent,
            'total_posted_jobs' => $this->Client->total_posted_jobs,
            'user_id'=>$this->Client->user_id
        ],
        'tags' => [
            'tags' => $this->tags->pluck('Tag_name')
        ],
        'Comments' => [
           'Comments' => ProjectCommentssResource::collection( $this->comments)
        ]
    ];
}

}
