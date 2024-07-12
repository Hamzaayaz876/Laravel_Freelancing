<?php

namespace App\Http\Controllers;

use App\Models\Freelancer;
use App\Models\Project;
use App\Models\ProjectComments;
use App\Models\reportComment;
use App\Models\reportFreelancer;
use App\Models\reportProject;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class reportController extends Controller
{
    use HttpResponses;

    public function ReportFreelancer(Request $request,string $id)
    {
        $currentUserId = Auth::user()->id;
        $freelancer=Freelancer::where('id',$id)->first();
        if($freelancer){
            $report=reportFreelancer::create([
                'Freelancer_id'=>$freelancer->id,
                'user_id'=>$currentUserId,
                'Report_type'=>$request->Report_type,
                'text'=>$request->text

            ]);
            return $this->success([
                'message'=>'This Freelancer was reported successfully'
            ]);
        }
        return response()->json([
            'message' => 'Freelancer not found',
        ], 404);
    }



    public function ReportProject(Request $request,string $id)
    {
        $currentUserId = Auth::user()->id;
        $project=Project::where('id',$id)->first();
        if($project){
            $report=reportProject::create([
                'project_id'=>$project->id,
                'user_id'=>$currentUserId,
                'Report_type'=>$request->Report_type,
                'text'=>$request->text

            ]);
            return $this->success([
                'message'=>'This Project was reported successfully'
            ]);
        }
        return response()->json([
            'message' => 'Project not found',
        ], 404);
    }






    public function ReportComment(Request $request,string $id)
    {
        $currentUserId = Auth::user()->id;
        $comment=ProjectComments::where('id',$id)->first();
        if($comment){
            $report=reportComment::create([
                'comment_id'=>$comment->id,
                'user_id'=>$currentUserId,
                'Report_type'=>$request->Report_type,
                'text'=>$request->text

            ]);
            return $this->success([
                'message'=>'This Project was reported successfully'
            ]);
        }
        return response()->json([
            'message' => 'Comment not found',
        ], 404);
    }

}
