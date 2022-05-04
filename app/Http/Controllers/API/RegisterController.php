<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\support\facades\Hash;
use Illuminate\Support\Facades\Auth;

use Validator;
class RegisterController extends Controller
{
    function register( Request $req){
        $req ->validate([
            'name'=>'required|string|max:150|min:2',
            'email'=>'required|string|max:150',
            'password'=>'required|max:150',
        ]);

        $user = new User;
        $user->name=$req->input('name');
        $user->email=$req->input('email');
        $user->password=Hash::make($req->input('password'));
        $user->save();
        $token = $user->createToken($user->email.'_Token')->plainTextToken;
        return response()->json([

            'username'=>$user->name,
            'token'=>$token,
            'message'=>'user registered succssfully',
         ],200);

    }

    function login( Request $req){
        $validate = Validator::make($req->all(),[
            'email'=>'required',
            'password'=>'required',
        ]);
        if($validate->fails())
        {
            return response()->json([

                'validators errors' => $validate->messages(),
             ],200);

        }
        $user = User::where('email',$req-> email)->first();
        if(!$user||!Hash::check($req->password, $user->password)){
            return ['error : email or password is not valied'];

        }else
        {
            /*$user = Auth::User();
            Session::put('user', $user);
            $user=Session::get('user');*/
            $token = $user->createToken($user->email.'_Token')->plainTextToken;
            return response()->json([

                'username'=>$user->name,
                'token'=>$token,
                'message'=>'user Login succssfully',
             ],200);
        }



        return $user;
    }

    public function logout(Request $req)
    {
        $user = User::where('token',$req->user()->tokens());
        $req->user()->tokens()->delete();
        $user=$req->user();
        return response()->json([
        'status' => 200,
        'message' => 'logout successfully',
        'username'=>$user->name,
        ]);
    }
    ///////////////////////////////////////////
    //////////////////////////////////////////
    function get_profile(Request $request){
        $user_id =$request->user()->id;
        $user =User::find($request->user()->id);
        if($user){
            return response()->json([
                'status' => 200,
                'message' => 'userprofile',
                'user'=>$user
                ]);

        }
        return response()->json([
            'status' => 500,
            'message' => ' userprofile not found'
                ]);

    }

    ////////////////////////////////////////////
    ///////////////////////////////////////////
    public function profileUpdate(Request $request){

        $Validator = Validator::make($request->all(),[
            'name' =>'required|min:2|string|max:150',
            'email'=>'require|email|unique:users,id,'.$request->user()->id ,
        ]);
        $user_id =$request->user()->id;
        $user =User::find($request->user()->id);
        $token = $request->bearerToken();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->update();
       // $user->save();
        return response()->json([
                'name'=>$user->name,
                'email'=>$user->email,
                'token'=>$token,
                'message'=>'user profile updated succssfully',
             ],200);
    }

    /////////////////////////////////
    ////////////////////////////////
    function delete_user($id){
        $user=User::where('id',$id)->delete();

        if($user){
            return response()->json([
                'message'=>'User deleted succssfully',
             ],200);
        }
        return response()->json([
            'message'=>'User not found',
         ],200);


    }
}
