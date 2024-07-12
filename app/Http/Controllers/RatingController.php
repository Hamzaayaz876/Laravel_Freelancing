<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddRatingRequest;
use App\Http\Resources\RatingResource;
use App\Models\Client;
use App\Models\Freelancer;
use App\Models\Project;
use App\Models\ProjectApplications;
use App\Models\Rating;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RatingController extends Controller
{
    use HttpResponses;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function ViewRating()
{
    $userId=Auth::user()->id;
    $freelancer=Freelancer::where('user_id',$userId)->first();
    if($freelancer){
        $ratings=Rating::where('Freelancer_id',$freelancer->id)->get();
        if($ratings->isEmpty()){
            return $this->success([
                'message'=>'You dont have any ratings. Cooperate with other users to have ratings'
            ]);
        }
        return RatingResource::collection($ratings);
    } else {
        return $this->error('','You are not a freelancer. Only freelancers have ratings.', 401);
    }
}


public function ViewFreelancerRating(string $id)
{


        $ratings=Rating::where('Freelancer_id',$id)->get();
        if($ratings->isEmpty()){
            return $this->success([
                'message'=>'this freelancer has no ratings'
            ]);
        }
        return RatingResource::collection($ratings);
    }


    /**
     * Show the form for editing the specified resource.
     */

    //     $freelancer=Freelancer::where('user_id',$userId)->first();
    //     if($freelancer){
    //         $applicationID=ProjectApplications::where('Freelancer_id',$freelancer->id)->where('Project_id',$id)->where('State','Successfull')->get('id');
    //         if($applicationID){
    //             $clientID=Project::where('id',$id)->get('client_id');
    //             $user=Client::where('id',$clientID)->get('user_id');
    //             if($user){
    //                 Rating::create([
    //                     'rated'=>$user,
    //                     'project_id'=>$id,
    //                     'who_rates'=>$userId,
    //                     'Review'=>$request->Review,
    //                     'number'=>$request->number
    //                 ]);
    //             }
    //             return $this->error('','You cant rate your cooperator now you need to have a project done first', 401);
    //         }
    //         return $this->error('','You cant rate your cooperator now you need to have a project done first', 401);
    //     }
    //     $client=Client::where('user_id',$userId)->first();
    //     if($client){
    //         $freelancerID=ProjectApplications::where('Project_id',$id)->where('State','Successfull')->get('Freelancer_id');
    //         $user2=Freelancer::where('id',$freelancerID)->get('user_id');
    //         if($user2){


    //         Rating::create([
    //             'rated'=>$userId,
    //             'project_id'=>$id,
    //             'who_rates'=>$user2,
    //             'Review'=>$request->Review,
    //             'number'=>$request->number
    //         ]);
    //     }
    //     return $this->error('','You cant rate your cooperator now you need to have a project done first', 401);
    // }
    public function AddRating(AddRatingRequest $request,string $id)
    {
        $userId=Auth::user()->id;
    $client=Client::where('user_id',$userId)->first();
    if($client){
        $projectState=Project::where('id',$id)->where('State','Done')->first();
        if($projectState){
            $freelancerID=ProjectApplications::where('Project_id',$id)->where('State','Successfull')->value('Freelancer_id');
            if($request->number <0 || $request->number >5 ){
                return $this->error('','You can choose rating just between 0 and 5', 401);

            }
            $rateonetime=Rating::where('Project_id',$id)->first();
            if($rateonetime){
                return $this->error('','You can Rate the freelancer that finish your project just one time', 401);
            }
            Rating::create([
                            'client_id'=>$client->id,
                            'Project_id'=>$id,
                            'Freelancer_id'=>$freelancerID,
                            'Review'=>$request->Review,
                            'number'=>$request->number
                        ]);

                        $allFreelancer_ratings = Rating::where('Freelancer_id', $freelancerID)->pluck('number');
$sum = $allFreelancer_ratings->sum();
$total = $allFreelancer_ratings->count();

$average = $total > 0 ? $sum / $total : 0;
$freelancer = Freelancer::where('id', $freelancerID)->first();
$freelancer->update([
    'Total_Rating' => $average,
    'Total_Rated_times'=>$total
]);
return $this->success([
    'message'=>'Rating has added successfully'
]);

        }

 return $this->error('','Your project must be done to get the permission to rate freelancer that work on it', 401);
    }
    return $this->error('','Login and as Client to add rating to a freelancer that completed your project', 401);
}

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
