<?php

namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class VerificationController extends Controller
{
    public function verify($user_id,Request $request)
    {
        if(!$request->hasValidSignature())
        {
            return response()->json(["msg"=>"Invalid/Expird url providers"],401);
        }
        $user = User::Where('id',$user_id)->first();
        if(!$user->hasVerifiedEmail())
        {
            $user->markEmailAsVerified();
            $user->update(['status'=>$user->status=1]);

        }else{
            return response()->json([
                'status'=>'403',
                'message'=>'Email already verified',
             ]);
        }
        return response()->json([
            'status'=>'200',
            'message'=>'your email successfully verified',
         ]);
    }
}
