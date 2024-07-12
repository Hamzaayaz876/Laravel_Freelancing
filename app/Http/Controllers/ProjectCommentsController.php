<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddcommentRequest;
use App\Http\Resources\ProjectCommentssResource;
use App\Models\Freelancer;
use App\Models\Project;
use App\Models\ProjectComments;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectCommentsController extends Controller
{
    use HttpResponses;

    /**
     * Store a newly created resource in storage.
     */

    public function AddComment(AddcommentRequest $request, string $id)
    {
        $request ->validated($request->all());

    $currentUserId = Auth::user()->id;
    $Freelancer = Freelancer::where('user_id', $currentUserId)->first();

    if (!$Freelancer) {
        return $this->error('','You are not authorized.', 422);
    }
    $Project=Project::where('id',$id)->first();
        if (!$Project) {
            return $this->error('','This project doesnot exist.', 422);
        }
    ProjectComments::create([
        'Freelancer_id'=>$Freelancer->id,
        'Project_id'=>$id,
        'Text'=>$request->Text,
        'State'=>$request->State
        ]
        );

        $nbOfComments=$Project->Comments_Number+1;
        $Project->update([
            'Comments_Number'=>$nbOfComments
        ]);
        return $this->success([
            'message'=>'Comment has been Created Successfully'
        ]);
    }



    public function UpdateComment(AddcommentRequest $request, string $id)
    {
        $request ->validated($request->all());

    $currentUserId = Auth::user()->id;
    $Freelancer = Freelancer::where('user_id', $currentUserId)->first();

    if (!$Freelancer) {
        return $this->error('','You are not authorized.', 422);
    }
    $Comments = ProjectComments::where('id', $id)->first();

    if (!$Comments) {
        return $this->error('', 'Comment not found.', 404);
    }
        $Comments->update([
        'Text'=>$request->Text,
        'State'=>$request->State
        ]
        );
        return $this->success([
            'message'=>'Comment has been Updated Successfully'
        ]);
    }
    /**
     * Display the specified resource.
     */

     //Show all comments kon a certain project
    public function showAllComments(string $id)
    {
        return ProjectCommentssResource::collection(
            ProjectComments::where('Project_id',$id)->get()
        );
    }



    /**
     * Remove the specified resource from storage.
     */
    public function deletComment(string $id)
    {
        ///Incompleted
        $currentUserId = Auth::user()->id;
    $Freelancer = Freelancer::where('user_id', $currentUserId)->first();

    if (!$Freelancer) {
        return $this->error('', 'You are not authorized.', 403);
    }

    $Comments = ProjectComments::where('id', $id)->first();

    if (!$Comments) {
        return $this->error('', 'Comment not found.', 404);
    }

    // Check if the client ID of the project matches the client ID of the user of the token
    if ($Freelancer->id !== $Comments->Freelancer_id) {
        return $this->error('', 'You are not authorized to update this comment.', 403);
    }
    $Project=Project::where('id',$Comments->Project_id)->first();
        if (!$Project) {
            return $this->error('','This project of this comment doesnot exist.', 422);
        }
        $Comments->delete();
        $nbOfComments=$Project->Comments_Number-1;
        $Project->update([
            'Comments_Number'=>$nbOfComments
        ]);
        return $this->success([
            'message'=>'Project deleted successfully'
        ]);


    }
}
