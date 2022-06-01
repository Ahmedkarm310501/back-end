<?php

namespace App\Http\Controllers\API;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Product;
use Illuminate\Support\Str;
use Illuminate\support\facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\forgetPassword;
use App\Mail\UserVerification;

use Validator;
class RegisterController extends Controller
{
    function register( Request $req){


        $validate= Validator::make($req ->all(),[
            'name'=>'required|max:150|min:2',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password'=>'required|max:150',
            'date_of_birth'=>'required',
        ]);
        if($validate->fails())
        {
            return response()->json([
                'status'=>'403',
                'validators errors' => $validate->messages(),
             ]);

        }
        $user = User::create([
            'name' =>$req->name,
            'email' =>$req->email,
            'date_of_birth' =>$req->date_of_birth,
            'password' =>Hash::make($req->password)]
        );
        if($user)
        {

                Mail::mailer('smtp')->to($user->email)->send(new UserVerification($user));
                return response()->json([
                    'status'=>'200',
                    'username'=>$user->name,
                    'message'=>'user registered succssfully,please verify your email address to login',
                 ]);
        }else{
            $user->delete();
            return response()->json([
                'status'=>'403',
                'message'=>'could not send email verification please try again',
             ]);
        }


    }

    /////////////////////////////////

    function login( Request $req){
        $validate = Validator::make($req->all(),[
            'name'=>'required',
            'password'=>'required',
        ]);
        if($validate->fails())
        {
            return response()->json([
                'status'=>'400',
                'validators errors' => $validate->messages(),
             ]);

        }
        $user = User::where('name',$req-> name)->first();
        if(!$user||!Hash::check($req->password, $user->password)){
            return ['status'=>'403',
            'error : email or password is not valied'];

        }else
        {
            $userdata = array(
                'name' => $req->name ,
                'password' => $req->password
              );

              if (Auth::attempt($userdata) && $user->status==1)
                {

                    $token = $user->createToken($user->name.'_Token')->plainTextToken;
                    $user->update(['token'=>$token]);


                    return response()->json([
                        'status'=>'200',
                        'session_time'=>env('SESSION_LIFETIME'),
                        'username'=>$user->name,
                        'token'=>$token,
                        'isAdmin'=>$user->Is_Admin,
                        'message'=>'user Login succssfully',
                     ]);

                }
                else
                {
                    return response()->json([

                        'status'=>'405',
                        'message'=>'not verified user',
                     ]);
                }


        }
    }

    public function logout(Request $request)
    {
        $validate= Validator::make($request ->all(),[

            'token' => 'required',

        ]);
        $user =User::where('token',$request->token)->first();
        $user->update(['token'=>null]);
        return response()->json([
        'status' => 200,
        'message' => 'logout successfully',
        ]);
    }
    ///////////////////////////////////////////
    //////////////////////////////////////////


///////////////////////////////////
public function reset_pasword(Request $request){
    $validate= Validator::make($request ->all(),[
        'token' => 'required',
        'password' => 'required|string|max:255',
        'newpassword' => 'required|string|max:255',
        'repassword' => 'required|string|same:newpassword',

    ]);
    $user =User::where('token','=',$request->token)->first();
    if($user!= null){
        if(Hash::check($request->password, $user->password)){
            $user->update(['password'=>Hash::make($request->newpassword)]);
            return response()->json([
                'status' => 200,
                'message' => 'password updated successfully',

                ]);
        }else{

            return response()->json([
                'status' => 403,
                'message' => 'password not correct',

                ]);
        }
    }else{
    return response()->json([
        'status' => 405,
        'message' => 'not auth user ',

        ]);

    }



}

// create forget password
public function forget_password(Request $request){
    $validate= Validator::make($request ->all(),[
        'email' => 'required|string|email|max:255',

    ]);
    $user =User::where('email','=',$request->email)->first();
    if($user!= null){
        $newPassword=Str::random(10);
        $user->update(['password'=>Hash::make($newPassword)]);
        Mail::to($user->email)->send(new forgetPassword($newPassword));
        return response()->json([
            'status' => 200,
            'message' => 'email sent successfully',

            ]);
    }else{
    return response()->json([
        'status' => 405,
        'message' => 'not auth user ',

        ]);

    }

}

// create function to check if email is exist
public function check_email(Request $request){
    $validate= Validator::make($request ->all(),[
        'email' => 'required|string|email|max:255',

    ]);
    $user =User::where('email','=',$request->email)->first();
    if($user!= null){
        return response()->json([
            'status' => 200,
            'message' => 'email exist',

            ]);
    }else{
        return response()->json([
            'status' => 405,
            'message' => 'email not exist',

            ]);

    }
}


// create function to check if username is exist
public function check_username(Request $request){
    $validate= Validator::make($request ->all(),[
        'name' => 'required|string|max:255',

    ]);
    $user =User::where('name','=',$request->name)->first();
    if($user!= null){
        return response()->json([
            'status' => 200,
            'message' => 'username exist',

            ]);
    }else{
        return response()->json([
            'status' => 405,
            'message' => 'username not exist',

            ]);

    }

}



}
