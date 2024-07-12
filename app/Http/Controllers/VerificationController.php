<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\URL;
use App\Traits\HttpResponses;


class VerificationController extends Controller
{
    use HttpResponses;
    public function verify(Request $request, $id)
    {
        $user = \App\Models\User::find($id);
        if (!$user) {
            return $this->error('','This user does not exist',404);
        }

        if (!URL::hasValidSignature($request)) {
            return $this->error('','Invalid verification link',401);
        }

        if ($user->hasVerifiedEmail()) {
            return $this->error('','Email already verified',422);
        }

        $user->markEmailAsVerified();
        event(new Verified($user));
        return redirect()->to('http://localhost:4200/login');
    }

    public function resend(Request $request)
    {
        $request->validate([
            'email' => ['email', 'required']
        ]);

        $user = \App\Models\User::where('email', $request->email)->first();
        if (!$user) {
            return $this->error('','User does not exist',404);
        }

        if ($user->hasVerifiedEmail()) {
            return $this->error('','Email already verified',422);
        }

        $user->sendEmailVerificationNotification();
        return $this->success([
            'message' => 'Verification email sent',
            'user' => $user
        ]);
    }
}
