<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateClientRequest;
use App\Http\Resources\ClientsResource;
use App\Models\Client;
use App\Models\Freelancer;
use App\Models\Freelancer_tags;
use App\Models\User;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClientsController extends Controller
{
    use HttpResponses;
    /**
     * Display a listing of the resource.
     */
    public function ShowClientBYID(string $id){
        $client=Client::where('id',$id)->first();
        if($client){
            return ClientsResource::collection(
                $client
            );
        }
        return [
            'message'=>'This freelancer is not availabe'
        ];
    }


    /**
     * Store a newly created resource in storage.
     */
    public function UpdateClient(UpdateClientRequest $request)
    {
        $request ->validated($request->all());

    $currentUserId = auth()->id();
    $Client = Client::where('user_id', $currentUserId)->first();

    if (!$Client) {
        return $this->error('','You are not authorized.', 422);
    }

    $Client->update([
        'company_name' => $request->company_name,
        'company_owners' => $request->company_owners,
        'website_link' => $request->website_link,
    ]);

    return $this->success([
        'message'=>'Client Was Updated successfully'
    ]);


    }
    public function editProfile(Request $request ){

        //$request ->validated($request->all());
        $currentUserId = Auth::user()->id;
        $user = User::find($currentUserId);

        if (!$user) {
            return $this->error('','You are not authorized.', 422);
        }

        // $usernameCheck = User::where('username', $request->username)
        //     ->where('id', '!=', $user->id)
        //     ->first();

        // if ($usernameCheck) {
        //     return $this->error('','Username already exists.', 422);
        // }

        $Client = Client::where('user_id', $currentUserId)->first();

        if ($Client) {


            // $user->update(
            //     [
            //         'username'=>$request->username
            //     ]
            //     );
        $Client->update([
            'company_name' => $request->company_name,
            'company_owners' => $request->company_owners,
            'website_link' => $request->website_link,
        ]);

        return $this->success([
            'message'=>'Your profile Was Updated successfully'
        ]);
    }
    $freelancer = Freelancer::where('user_id', $currentUserId)->first();

    if ($freelancer) {



    $picture = null;
    $cv = null;

    if ($request->hasFile('picture')) {
        $picture = $request->file('picture')->get();
    }

    if ($request->hasFile('cv')) {
        $cv = file_get_contents($request->file('cv')->getRealPath());
    }

    // $user->update(
    //     [
    //         'username'=>$request->username
    //     ]
    //     );
    $freelancer->update([
        'firstname' => $request->firstname,
        'lastname' => $request->lastname,
        'skill_name'=>$request->skill_name,
        'bio' => $request->bio,
        'picture' => $picture,
        'cv' => $cv,
        'Category'=>$request->Category
    ]);
    if ($request->tags) {
        $tags = explode(',', $request->tags);

        // Add each tag to the project_tags table
        foreach ($tags as $tag) {
            Freelancer_tags::create([
                'Tag_name' => trim($tag),
                'Freelancer_id' => $freelancer->id
            ]);
        }
        return $this->success([
            'message'=>'Your profile Was Updated successfully'
        ]);
    }}


}
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
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
