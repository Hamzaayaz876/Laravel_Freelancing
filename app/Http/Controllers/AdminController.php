<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use App\Http\Requests\FreezeUserRequest;
use App\Http\Requests\LoginAdminRequest;
use App\Http\Resources\ClientsResource;
use App\Http\Resources\FreelancersResource;
use App\Http\Resources\reportCommentResource;
use App\Http\Resources\reportFreelancerResource;
use App\Http\Resources\reportProjectResource;
use App\Models\Admin;
use App\Models\Client;
use App\Models\Conversation;
use App\Models\Freelancer;
use App\Models\moneyHandler;
use App\Models\Project;
use App\Models\ProjectApplications;
use App\Models\reportComment;
use App\Models\reportFreelancer;
use App\Models\reportProject;
use App\Models\User;
use App\Traits\HttpResponses;
use Illuminate\Support\Facades\Auth;

use Illuminate\Validation\ValidationException;

class AdminController extends Controller
{
    use HttpResponses;
    /**
     * Display a listing of the resource.
     */
    public function registerAdmin(Request $request)
    {
        $request->validate([
            'username' => 'required|string|unique:admins',
            'password' => 'required|string|min:6',
        ]);

        $adminer = Admin::create([
            'username' => $request->username,
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'adminer' => $adminer,
        ], 201);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function loginAdmin(LoginAdminRequest $request){
        $request->validated();

        $admin= Admin::where('username',$request->username)->first();

        if(!$admin || !Hash::check($request->password, $admin->password)){
            return $this->error('','Incorrect username or password ',401);
        }

        $token = $admin->createToken('Admin Token')->plainTextToken;

        //return all admin info with the token of this user
        return $this->success([
            'user'=>$admin,
            'token' =>$token
        ]);

    }



    /**
     * Store a newly created resource in storage.
     */
    public function BanUser(string $id){
        $user = User::find($id);
        if($user){
            if($user->State != 'Banned'){
                $user->update([
                    'State'=>'Banned'
                ]);
                $user->tokens()->delete();
                return $this->success([
                    'message'=>'User was banned successfully'
                ]);
            } else {
                return $this->error('', 'User is already banned', 200);
            }
        } else {
            return $this->error('', 'User not found', 404);
        }
    }




    public function unBanUser(string $id){
        $user = User::find($id);
        if($user){
            if($user->State == 'Banned'){
                $user->update([
                    'State'=>'Available'
                ]);
                return $this->success([
                    'message'=>'User was unbanned successfully'
                ]);
            } else {
                return $this->error('', 'User is already unbanned', 200);
            }
        } else {
            return $this->error('', 'User not found', 404);
        }
    }


    public function Freeze(string $id,FreezeUserRequest $request){
        $user = User::find($id);
        if($user){
            if($user->freeze ==null){
                $user->update([
                    'freeze'=>$request->freeze
                ]);
                $user->tokens()->delete();
                return $this->success([
                    'message' => 'User was Freezed successfully'
                ]);
            } else {
                return $this->error('', 'User is already freezed', 200);
            }
        } else {
            return $this->error('', 'User not found', 404);
        }
    }




    public function unFreeze(string $id){
        $user = User::find($id);
        if($user){
            if($user->freeze !=null){
                $user->update([
                    'freeze'=>null
                ]);

                return $this->success([
                    'message' => 'The freeze was removed from the User successfully'
                ]);
            } else {
                return $this->error('', 'User is already unfreezed', 200);
            }
        } else {
            return $this->error('', 'User not found', 404);
        }
    }



    public function deleteUser($id)
{
    $user = User::find($id);

    if(!$user) {
        return response()->json([
            'message' => 'User not found',
        ], 404);
    }

    $user->delete();

    return response()->json([
        'message' => 'User deleted successfully',
    ], 200);
}










public function deleteProjectByAdmin(string $id)
{
    $project = Project::find($id);

    if (!$project) {
        return $this->error('', 'Project not found.', 404);
    }

    $clientID = $project->client_id;
    $userID=Client::where('id',$clientID)->value('user_id');
    $user = User::where('id', $userID)->first();

    $money_amount=$user->money_amount;
    $real_money = $money_amount;
    $newammount = $real_money + $project->Budget;
    if($user) {
        $moneyHandler = MoneyHandler::where('project_id', $project->id)->first();
        if($moneyHandler) {
            $moneyHandler->delete();
        }



        $user->update([
            'money_amount' => $newammount,
        ]);
    }

    $project->delete();

    return $this->success([
        'message' => 'Project deleted successfully'
    ]);
}

public function DoneProjectByAdmin(string $id){

    $project=Project::where('id',$id)->first();
    if(!$project){
        return $this->error('','This Project doesnot exist..', 422);
    }
$Client = Client::where('id', $project->client_id)->first();


    $Application=ProjectApplications::where('Project_id',$project->id)->where('State','Accepted')->first();
    if (!$Application) {
        return $this->error('','You cant make it as done until you have accepted an application', 422);
    }
    if($project->State == 'Done') {
        return $this->error('', 'This Application is already done ', 404);
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

    //I want it with search bar that admin can search by usernames
    public function searchByUsername(string $username)
{
    $user = User::where('username', $username)->first();
    if ($user) {
        if ($user->position == 'freelancer') {
            $freelancer = Freelancer::where('user_id', $user->id)->first();
            return new FreelancersResource($freelancer);
        }
        if ($user->position == 'client') {
            $client = Client::where('user_id', $user->id)->first();
            return new ClientsResource($client);
        }
    }

    return response()->json([
        'message' => 'User not found',
    ], 404);
}



    public function VeiwbyuserID(string $id)
    {
        $user = User::find($id);
        if ($user) {
            if ($user->position == 'freelancer') {
                $freelancer = Freelancer::where('user_id', $user->id)->first();
                return new FreelancersResource($freelancer);
            }
            if ($user->position == 'client') {
                $client = Client::where('user_id', $user->id)->first();
                return new ClientsResource($client);
            }
        }

        return response()->json([
            'message' => 'User not found',
        ], 404);
    }




    //FREELANCERS REPORTS
    public function DeleteReportFreelancer(string $id)
    {
        $report=reportFreelancer::where('id',$id)->first();
        if($report){
            $report->delete();
            return $this->success([
                'message'=>'Report was deleted successfully'
            ]);
        }
        return response()->json([
            'message' => 'This report is already deleted',
        ], 404);
    }

    public function ViewReportFreelancer(string $id)
    {
        $report=reportFreelancer::where('id',$id)->first();
        if($report){

            return new reportFreelancerResource($report);
        }
        return response()->json([
            'message' => 'Report not found. ',
        ], 404);
    }


    public function showReportedFreelancers()
    {
        return reportFreelancerResource::collection(
            reportFreelancer::all()
        );
    }




    //PROJECTS REPORTS
    public function DeleteReportProject(string $id)
    {
        $report=reportProject::where('id',$id)->first();
        if($report){
            $report->delete();
            return $this->success([
                'message'=>'Report was deleted successfully'
            ]);
        }
        return response()->json([
            'message' => 'This report is already deleted',
        ], 404);
    }


    public function ViewReportProject(string $id)
    {
        $report=reportProject::where('id',$id)->first();
        if($report){

            return new reportProjectResource($report);
        }
        return response()->json([
            'message' => 'Report not found. ',
        ], 404);
    }


    public function showReportedProjects()
    {
        return reportProjectResource::collection(
            reportProject::all()
        );
    }




    //COMMENT REPORTS

    public function DeleteCommentProject(string $id)
    {
        $report=reportComment::where('id',$id)->first();
        if($report){
            $report->delete();
            return $this->success([
                'message'=>'Report was deleted successfully'
            ]);
        }
        return response()->json([
            'message' => 'This report is already deleted',
        ], 404);
    }


    public function ViewCommentProject(string $id)
    {
        $report=reportComment::where('id',$id)->first();
        if($report){

            return new reportCommentResource($report);
        }
        return response()->json([
            'message' => 'Report not found. ',
        ], 404);
    }


    public function showCommentProjects()
    {
        return reportCommentResource::collection(
            reportComment::all()
        );
    }
}
