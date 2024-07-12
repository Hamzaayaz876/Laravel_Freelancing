<?php

namespace App\Http\Controllers;

use App\Http\Resources\ClientConversationResourse;
use App\Models\Client;
use App\Models\Conversation;
use App\Models\Freelancer;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Concersation extends Controller
{


    /**
     * Show the form for creating a new resource.
     */
    public function ShowConversations()
    {
        if (Auth::check()) {
            $userId = Auth::user()->id;

            $client=Client::where('user_id',$userId)->first();

            if($client){
                $conversations=Conversation::where('client_id',$client->id)->get();
                if($conversations){
            return ClientConversationResourse::collection($conversations->get()
        );}
        return [
            'message'=>'You dont have any conversations yet'
        ];

    }
        $freelancer=Freelancer::where('user_id',$userId)->first();
        if($freelancer){
            $conversations=Freelancer::where('Freelancer_id',$freelancer->id)->get();
            if($conversations){
        return Freelancer::collection($conversations->get()
    );}


            return [
                'message'=>'You dont have any conversations yet'
            ];

    }
        else{
            return $this->errorResponse('Login first ', 401);
        }
    }

    }
    public function DeleteConversation(string $id)
    {

    }
}
