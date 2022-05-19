<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Product;

use Illuminate\support\facades\Hash;
use Illuminate\Support\Facades\Auth;

use Validator;
class RegisterController extends Controller
{
    function register( Request $req){

    
        $validate= Validator::make($req ->all(),[
            'name'=>'required|max:150|min:2',
            'email' => 'required|string|email|max:255|unique:users,email',

            'password'=>'required|max:150',
        ]);
        if($validate->fails())
        {
            return response()->json([

                'validators errors' => $validate->messages(),
             ],200);

        }
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
            $userdata = array(
                'email' => $req->email ,
                'password' => $req->password
              );
              // attempt to do the login
              if (Auth::attempt($userdata))
                {

                    $token = $user->createToken($user->email.'_Token')->plainTextToken;
                    $user->update(['token'=>$token]);

                    return response()->json([
                        'session_time'=>env('SESSION_LIFETIME'),
                        'username'=>$user->name,
                        'token'=>$token,
                        'message'=>'user Login succssfully',
                     ],200);

                }
                else
                {
                    return response()->json([


                        'message'=>'error invaild data',
                     ],403);
                }

            /*$user = Auth::User();
            Session::put('user', $user);
            $user=Session::get('user');*/




        }



    }

    public function logout(Request $req)
    {
        User::findOrFail(Auth::user()->id)->update(['token'=>null]);

        Auth::logout();

        return response()->json([
        'status' => 200,
        'message' => 'logout successfully',
      //  'username'=>$user->name,
        ]);
    }
    ///////////////////////////////////////////
    //////////////////////////////////////////
   public function get_profile(Request $request){
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

   public function profile(Request $request){
        $validate = Validator::make($request->all(),[
            'token'=>'required',

        ]);
    //$token=$request->token;
    //echo $token;
    $token = $request->bearerToken();
    $user= Auth::user();
    $user->name = $request->name;
    $user->email = $request->email;
    return response()->json([
        'status' => 200,
        'message' => 'logout successfully',
        'username'=>$user->name,
        ]);
    }

    ////////////////////////////////////////////
    ///////////////////////////////////////////
    public function profileUpdate(Request $request){


        $Validator = Validator::make($request->all(),[
            'name' =>'required|min:2|string|max:150',
            'email'=>'require|email|unique:users,id,'.$request->user()->id ,
        ]);
       // $user_id =$request->user()->id;
        $user =User::where('id','=',$request->id)->where('token','=',$request->token)->frist();
        if($user != null){
          //  $token = $request->bearerToken();
            $user= Auth::user();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->update();
            return response()->json([
                    'name'=>$user->name,
                    'email'=>$user->email,
                    //'token'=>$token,
                    'message'=>'user profile updated succssfully',
                 ],200);

        }else
        {
            return response()->json([

                'message'=>'error',
             ],403);


        }
    }

    /////////////////////////////////
    ////////////////////////////////
   public function delete_user($id){
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
    public function getDetails(Request $req){
        $product = Product::where('id','=',$req->id)->with('photos')->first();

       // $product;
       //dd($product);
       return response()->json([

        'message' => $product

        ]);


    }


}
