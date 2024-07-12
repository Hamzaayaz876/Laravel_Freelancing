<?php

namespace App\Http\Controllers;

use App\Http\Requests\Addapplication;
use App\Http\Resources\ProjectApplicationsResource;
use App\Http\Resources\ProjectsResource;
use App\Models\Client;
use App\Models\Conversation;
use App\Models\Freelancer;
use App\Models\moneyHandler;
use App\Models\Project;
use App\Models\ProjectApplications;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\FreelancerApplicationAccepted;

class ProjectApplicantsController extends Controller
{
    use HttpResponses;





    //shown application by id


    public function Addapplication(Addapplication $request,string $id)
{
    $request ->validated($request->all());

    $currentUserId = Auth::user()->id;
    $Freelancer = Freelancer::where('user_id', $currentUserId)->first();

    if (!$Freelancer) {
        return $this->error('','You are not authorized.', 422);
    }

    $Project = Project::where('id', $id)->first();

    if (!$Project) {
        return $this->error('','This project doesnot exist.', 422);
    }

    //Check if the project's state is not 'Pending'
    if ($Project->State !== 'Pending') {
        return $this->error('','This project is already Inprogress or Done.', 422);
    }

    //To check if the application has been sent before for the same application
    $applicationBefore = ProjectApplications::where('Project_id', $id)
        ->where('Freelancer_id', $Freelancer->id)
        ->exists();

    if ($applicationBefore) {
        return $this->error('', 'You cannot send more than one application to the same project.', 422);
    }

    ProjectApplications::create([
        'Freelancer_id'=>$Freelancer->id,
        'Project_id'=>$id,
        'Cover_Letter'=>$request->Cover_Letter,
    ]);

    return $this->success([
        'message'=>'Application has been Sent Successfully'
    ]);
}




    public function UpdateApplication(Addapplication $request, string $id)
    {
        $request ->validated($request->all());

    $currentUserId = Auth::user()->id;
    $Freelancer = Freelancer::where('user_id', $currentUserId)->first();

    if (!$Freelancer) {
        return $this->error('','You are not authorized.', 422);
    }
    $Aplication = ProjectApplications::where('id', $id)->where('State',);

    if (!$Aplication) {
        return $this->error('', 'Application not found or has been rejected.', 404);
    }


        $Aplication->update([
        'Cover_Letter'=>$request->Cover_Letter
        ]
        );
        return $this->success([
            'message'=>'Your upplication has been Updated Successfully'
        ]);
    }




//Accepted

public function AcceptApplication(string $id) {
    $currentUserId = Auth::user()->id;
    $Client = Client::where('user_id', $currentUserId)->first();

    if (!$Client) {
        return $this->error('', 'You are not authorized Login as Client First.', 422);
    }

    $Application = ProjectApplications::find($id);
    if (!$Application) {
        return $this->error('', 'This Application does not exist.', 422);
    }

    $project = Project::find($Application->Project_id);
    if (!$project) {
        return $this->error('', 'This Project does not exist..', 422);
    }

    if ($project->State != 'Pending') {
        return $this->error('', 'This Application is already done or in progress.', 404);
    }

    if ($project->client_id != $Client->id) {
        return $this->error('', 'You are not authorized this project is not yours.', 422);
    }

    $project->update([
        'State' => 'Inprogress'
    ]);

    $Application->update([
        'State' => 'Accepted'
    ]);

    ProjectApplications::where('Project_id', $Application->Project_id)->where('id', '!=', $Application->id)->update([
        'State' => 'Rejected'
    ]);

    Conversation::create([
        'Project_id' => $project->id,
        'Freelancer_id' => $Application->Freelancer_id,
        'client_id' => $Client->id
    ]);

    $freelancer = Freelancer::find($Application->Freelancer_id);
    $moneyhandler = MoneyHandler::where('Project_id', $project->id)->first();

    $moneyhandler->update([
        'Freelancer_id' => $freelancer->id
    ]);
    $freelancerEmail = $freelancer->user->email;
    $clientCompanyName = $Client->company_name;
    $clientEmail = $Client->user->email;

    Mail::to($freelancerEmail)->send(new FreelancerApplicationAccepted($freelancer->name, $clientCompanyName, $clientEmail));

    return $this->success([
        'message' => 'Application has been Accepted Successfully'
    ]);
}




//Rejected

public function RejectApplication(string $id){
    $currentUserId = Auth::user()->id;
$Client = Client::where('user_id', $currentUserId)->first();

if (!$Client) {
    return $this->error('','You are not authorized Login as Client First.', 422);
}
$Application=ProjectApplications::where('id',$id)->first();
    if (!$Application) {
        return $this->error('','This Application doesnot exist.', 422);
    }
    $project=Project::where('id',$Application->Project_id)->first();
    if(!$project){
        return $this->error('','This Project doesnot exist..', 422);
    }
    $Client=Client::where('id',$project->client_id)->first();
    if(!$Client){
        return $this->error('','You are not authorized this project is not yours.', 422);
    }
    $Application->update([
        'State'=>'Rejected'
    ]);
    return $this->success('','Application has rejected successfully', 200);

}


public function showAppliedApplications(){

    if (Auth::check()) {
        $userId = Auth::user()->id;
        $client=Client::where('user_id',$userId)->first(); // use first instead of get
        if($client){
            $projects=Project::where('client_id',$client->id)->get(); // use get() without 'id'
            if($projects){
            $Appliedprojects=ProjectApplications::whereIn('Project_id',$projects->pluck('id'))->get(); // use whereIn and pluck id
            if($Appliedprojects){
            return ProjectApplicationsResource::collection($Appliedprojects);
            }
            else {
                return $this->success('','You dont have any applications', 200);
            }
        }
    else{
        return $this->success('','You dont have any project', 200);
    }
    }
        else {
            return $this->errorResponse('You are not Client', 403);
        }
    }
    else {
        return $this->errorResponse('You are not logged in', 401);
    }
}


    /**
     * Display the specified resource.
     */
    public function showMyApplications()
    {

        if (Auth::check()) {
            $userId = Auth::user()->id;
            $freelancer=Freelancer::where('user_id',$userId)->first();
            if($freelancer){
            return ProjectApplicationsResource::collection(ProjectApplications::where('Freelancer_id', $freelancer->id)->get());
        }
        else
    {
        return $this->errorResponse('You are not Freelancer', 403);
    }
    }
        else{
            return $this->errorResponse('You are not logged in', 401);
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function deleteApplication(string $id)
    {
        $currentUserId = Auth::user()->id;

    $freelancer = Freelancer::where('user_id', $currentUserId)->first();

    if (!$freelancer) {
        return $this->error('', 'You are not authorized.', 403);
    }

    $application = ProjectApplications::where('id', $id)->first();

    if (!$application) {
        return $this->error('', 'Apllication not found.', 404);
    }

    // Check if the client ID of the project matches the client ID of the user of the token
    if ($freelancer->id !== $application->Freelancer_id) {
        return $this->error('', 'You are not authorized to update this project.', 403);
    }
        $moneyhandler=moneyHandler::where('Freelancer_id',$freelancer->id)->get();
        $moneyhandler=$moneyhandler->where('Project_id',$application->Project_id)->get();
        if($moneyhandler){
            $moneyhandler->delete();
        }
        $application->delete();
        return $this->success([
            'message'=>'Project deleted successfully'
        ]);

    }

}
