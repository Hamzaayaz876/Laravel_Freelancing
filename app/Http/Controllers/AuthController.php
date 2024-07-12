<?php

namespace App\Http\Controllers;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable;
use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\ChangeNameRequest;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\StoreUserRequest;
use App\Http\Resources\ClientsResource;
use App\Http\Resources\FreelancersResource;
use App\Models\Client;
use App\Models\Freelancer;
use Illuminate\Support\Facades\Auth;
class AuthController extends Controller
{
    use HttpResponses;
    public function login(LoginUserRequest $request){
        $request ->validated($request->all());
        if(!Auth::attempt($request->only('email','password'))){
            return $this->error('','Incorrect email or password ',401);
        }
        $user= User::where('email',$request->email)->first();
        //if email was not verifyed
        if (!$user->hasVerifiedEmail()) {
            return $this->error('','Your email address is not verified', 403);
        }
        //if the account is banned
        if($user->State=='Banned'){
            return $this->error('','Your account is Banned', 403);
        }

        if($user->freeze){
            $timeLeft = $user->getTimeLeftForFutureDate();

if ($timeLeft) {
    // The datetime attribute is in the future
    return $this->error('', 'Your account is Frozen for ' . $timeLeft, 403);

}
$user->freeze = null;
}

if($user->position=='freelancer'){

    $freelancer=Freelancer::where('user_id',$user->id)->first();
    $user_info = new FreelancersResource($freelancer);
}
else


if($user->position=='client'){
    $client=Client::where('user_id',$user->id)->first();
    $user_info = new ClientsResource($client);
}
        //return all user info with the token of this user
        return $this->success([
            'user'=>$user_info,
            'token' => $user ->createToken('Api Token of '.$user->name)->plainTextToken
        ]);
    }


    public function changeName(ChangeNameRequest $request) {

        $user = Auth::user();
        $username = $request->input('username');

        // Update the user's name
        $user->username = $username;
        $user->save();

        return $this->success([
            'message'=>'Username changed successfully'
        ]);
    }
    public function changepassword(ChangePasswordRequest $request){
        $user = Auth::user();
        $oldPassword = $request->input('old_password');
        $newPassword = $request->input('new_password');

        // Check if the old password is correct
        if (!Hash::check($oldPassword, $user->password)) {
            return $this->error('', 'The old password is incorrect', 422);
        }

        // Update the user's password with the new one
        $user->password = Hash::make($newPassword);
        $user->save();

        return $this->success([
            'message'=>'Password changed successfully'
        ]);
    }


    public function register(StoreUserRequest $request){
        $request ->validated($request->all());
        if (User::where('username', $request->username)->exists()) {
            return $this->error('','Username was already taken.',422);
        }
        if (User::where('email', $request->email)->exists()) {
            return $this->error('','Email already registered.',422);
        }

//add to user table
        $user = User::create([
            'username' => $request->username,
            'email'=> $request->email,
            'password' => Hash::make($request->password),
            'position'=>$request->position
        ]);
        if($request->position=='client'){
            $client=Client::create([
                'user_id'=>$user->id,
            ]);
        }
        else{
            //metl le fow2
            Freelancer::create([
                'user_id'=>$user->id,
            ]);
        }
        $user->sendEmailVerificationNotification();

        return $this->success([
            'user'=>$user,
//deleted token
        ]);
    }

    public function logout(){
        Auth::user()->currentAccessToken()->delete();
        return $this->success([
            'message'=>'You have successfully loged out'
        ]);
    }
}
