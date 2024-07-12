<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class reportCommentResource extends JsonResource
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
                'Reported_Project' => [
                    'Comment' => new ProjectCommentssResource( $this->comment)
                ],
                'The_one_who_reports'=>$this->user_id
            ]
        ];
    }
}
