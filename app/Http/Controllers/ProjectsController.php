<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddJobRequest;
use App\Http\Resources\privateComments;
use App\Http\Resources\ProjectsResource;
use App\Models\Client;
use App\Models\Conversation;
use App\Models\Freelancer;
use App\Models\moneyHandler;
use App\Models\Project;
use App\Models\project_tags;
use App\Models\ProjectApplications;
use App\Models\User;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectsController extends Controller
{
    use HttpResponses;
    /**
     * Display a listing of the resource.
     */
    //show ALL projects we can use search also or not and search for tag_name and skill_name

    //Set The job to done --> job was ended successfully
    public function Done(string $id){
        $currentUserId = Auth::user()->id;
    $Client = Client::where('user_id', $currentUserId)->first();

    if (!$Client) {
        return $this->error('','You are not authorized Login as Client First.', 422);
    }
        $project=Project::where('id',$id)->first();
        if(!$project){
            return $this->error('','This Project doesnot exist..', 422);
        }
        $Application=ProjectApplications::where('Project_id',$project->id)->where('State','Accepted')->first();
        if (!$Application) {
            return $this->error('','You cant make it as done until you have accepted an application', 422);
        }
        if($project->State == 'Done') {
            return $this->error('', 'This Application is already done ', 404);
        }

        $Client=Client::where('id',$project->client_id)->first();
        if(!$Client){
            return $this->error('','You are not authorized this project is not yours.', 422);
        }
        $freelancer=Freelancer::where('id',$Application->Freelancer_id)->first();
        $userOfFreelancer=User::where('id',$freelancer->id)->first();
        $freelancer->update([
            'total_compeleted_jobs'=>$freelancer->total_compeleted_jobs +1
        ]);
        $project->update([
            'State'=>'Done'
        ]);
        $Application->update([
            'State'=>'Successfull'
        ]);
        $moneyhandler=moneyHandler::where('Project_id',$project->id)->first();

        $moneyOfFreelancer=$userOfFreelancer->money_amount+$moneyhandler->amountOfMoney;
        $userOfFreelancer->update([
            'money_amount'=>$moneyOfFreelancer
        ]);
        $moneyhandler->delete();
        $conversation=Conversation::where('Project_id',$project->id)->get();

        $conversation=$conversation->where('Project_id',$project->id)->first();
        if($conversation){
            $conversation->update([
                'State'=>'Closed'
            ]);
        }

        return $this->success([
            'message'=>'Application has been Aceepted Successfully'
        ]);


    }




    public function ShowAll($search = null)
    {

        if ($search) {
            $projectsBySkill = Project::where('skill_name', 'like', '%' . $search . '%');

            $tags = project_tags::where('Tag_name', 'like', '%' . $search . '%')->get();
            if ($tags->count() > 0) {
                $projectsByTag = Project::whereIn('client_id', $tags->pluck('client_id'));

                $projects = $projectsBySkill->union($projectsByTag);
            } else {
                $projects = $projectsBySkill;
            }
            $projects=$projects::where('State','Pending');
            return ProjectsResource::collection($projects->get());
        } else {
            $allprojects=Project::where('State','Pending');
            return ProjectsResource::collection($allprojects->get());
        }

    }

    //show projects by category
    public function showProject_Category(string $category)
    {
        $query= Project::where('State','Pending');
        $projects=$query->where('Category',$category);
        $projects=$projects::where('State','Pending');
        return ProjectsResource::collection(
            $projects->get()
        );
    }


    public function showProjectById(string $id){
        $Project = Project::where('id', $id)->first();

    if (!$Project) {
        return $this->error('', 'Project not found.', 404);
    }
    return new ProjectsResource(
        $Project
    );
    }

    //show Client his own projects
    public function showMyProjects()
    {

        if (Auth::check()) {
            $userId = Auth::user()->id;
            $client=Client::where('user_id',$userId)->first();

            if($client){
            return ProjectsResource::collection(Project::where('client_id', $client->id)->get());
        }
        else
    {
        return $this->errorResponse('You are not Client', 401);
    }
    }
        else{
            return $this->errorResponse('You are not logged in', 401);
        }
    }

//show all projects by descending order
    public function showAllProjectsDesc($search = null)
    {
        $query= Project::where('State','Pending');
        $query =$query->orderByDesc('budget');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('skill_name', 'like', '%' . $search . '%')
                    ->orWhereHas('tags', function ($q) use ($search) {
                        $q->where('Tag_name', 'like', '%' . $search . '%');
                    });
            });
        }

        $projects = $query->get();

        return ProjectsResource::collection($projects);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function Addproject(AddJobRequest $request)
    {
        $request ->validated($request->all());

    $userId = Auth::user()->id;
    $Client = Client::where('user_id', $userId)->first();
        $user=User::where('id',$userId)->first();
    if (!$Client) {
        return $this->error('','You are not authorized.', 422);
    }
    $real_money=$user->money_amount;
    if($request->Budget > $real_money){
        return $this->error('','You dont have enough money .', 201);
    }
    $project =Project::create([
    'client_id'=>$Client->id,
    'Title'=>$request->Title,
    'Description'=>$request->Description,
    'Level'=>$request->Level,
    'Budget'=>$request->Budget,
    'Application_Dealine'=>$request->Application_Dealine,
    'skill_name'=>$request->skill_name,
    'Category'=>$request->Category
    ]
    );
    $nbOfjb=$Client->total_posted_jobs+1;
    $TotalSpend=$Client->total_spent+$request->Budget;
    $Client->update([
        'total_posted_jobs'=>$nbOfjb,
        'total_spent'=>$TotalSpend
    ]);
    $newammount=$real_money-$request->Budget;
    $user->update([
        'money_amount'=>$newammount,
    ]);

    moneyHandler::create([

        'amountOfMoney'=>$request->Budget,
        'Project_id' => $project->id,
    ]);


    if ($request->tags) {
        $tags = explode(',', $request->tags);

        // Add each tag to the project_tags table
        foreach ($tags as $tag) {
            project_tags::create([
                'Tag_name' => trim($tag),
                'project_id' => $project->id
            ]);
        }
    }
    return $this->success([
        'message'=>'Project was added successfully. '
    ]);
    }


    /**
     * Update the specified resource in storage.
     */
    public function updateProject(Request $request, string $id)
    {
        $currentUserId = Auth::user()->id;
    $Client = Client::where('user_id', $currentUserId)->first();

    if (!$Client) {
        return $this->error('', 'You are not authorized.', 403);
    }

    $Project = Project::where('id', $id)->first();

    if (!$Project) {
        return $this->error('', 'Project not found.', 404);
    }

    // Check if the client ID of the project matches the client ID of the user of the token
    if ($Client->id !== $Project->client_id) {
        return $this->error('', 'You are not authorized to update this project.', 403);
    }
        $Project->update([
    'Title'=>$request->Title,
    'Description'=>$request->Description,
    'Level'=>$request->Level,
    'Budget'=>$request->Budget,
    'skill_name'=>$request->skill_name,
    'Application_Dealine'=>$request->Application_Dealine,
    'Category'=>$request->Category
        ]);
        if ($request->tags) {
            $tags = explode(',', $request->tags);
            // Delete all tags
            project_tags::where('project_id',$id)->delete();
            // Add each tag to the project_tags table
            foreach ($tags as $tag) {

                project_tags::create([
                    'Tag_name' => trim($tag),
                    'project_id' => $Project->id
                ]);
            }
        }

        // Return a success response
        return $this->success([
            'message'=>'Project updated successfully. '
        ]);

    }

    public function getAppliedProjects()
{
    if (Auth::check()) {
        $userId = Auth::user()->id;
        $freelancer = Freelancer::where('user_id', $userId)->first();

        if ($freelancer) {
            $myApplications = ProjectApplications::where('Freelancer_id', $freelancer->id)->get('Project_id');

            if ($myApplications->isNotEmpty()) {
                $projectIds = $myApplications->pluck('Project_id');
                $projects = Project::whereIn('id', $projectIds)->get();

                return ProjectsResource::collection($projects);
            } else {
                return $this->error('','You don\'t have any applications', 401);
            }
        } else {
            return $this->error('','You are not a Freelancer', 401);
        }
    } else {
        return $this->error('','You are not logged in', 401);
    }
}


    /**
     * Remove the specified resource from storage.
     */
    public function deleteProject(string $id)
    {
        $currentUserId = Auth::user()->id;

    $Client = Client::where('user_id', $currentUserId)->first();

    if (!$Client) {
        return $this->error('', 'You are not authorized.', 403);
    }

    $Project = Project::where('id', $id)->first();

    if (!$Project) {
        return $this->error('', 'Project not found.', 404);
    }

    // Check if the client ID of the project matches the client ID of the user of the token
    if ($Client->id !== $Project->client_id) {
        return $this->error('', 'You are not authorized to update this project.', 403);
    }
        $Project->delete();
        return $this->success([
            'message'=>'Project deleted successfully'
        ]);

    }

}
