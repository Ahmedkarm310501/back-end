<?php

namespace App\Http\Controllers\API;
use Illuminate\Http\UploadedFile;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Product;
use Illuminate\Support\Str;
use Illuminate\support\facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\forgetPassword;

use Validator;

class UserController extends Controller
{
    public function addUser(Request $req){/////////work

        $validate= Validator::make($req ->all(),[
            'name'=>'required|max:150|min:2',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password'=>'required|max:150',
            'token'=>'required',
            'date_of_birth'=>'required',
            'status'=>'required',
            'Is_Admin'=>'required'
        ]);
        if($validate->fails())
        {
            return response()->json([
                'status'=>'403',
                'validators errors' => $validate->messages(),
             ]);
    
        }
    
        $admin = User::where('token','=',$req->token)->first();
            if($admin->Is_Admin==1){
                $user = new User;
                $user->name = $req->input('name');
                $user->email = $req->input('email');
                $user->password = Hash::make($req->input('password'));
                $user->date_of_birth = $req->date_of_birth;
                $user->status = $req->status;
                $user->Is_Admin = $req->Is_Admin;
                $user->save();
                return response()->json([
                    'status'=>'200',
                    'name'=>$user,
                    'message'=>'user added succssfully',
                ]);
            }
            else{
                return response()->json([
                    'status'=>'407',
                    'message'=>'out of your privileges',
                 ]);
            }
    
    
        }


/////////////////////////////////////////
public function deleteUser(Request $req){///////////////user delete himself///////work
    $validate = Validator::make($req ->all(),[
        'token'=>'required',
    ]);
    if($validate->fails())
    {
        return response()->json([
            'status'=>'403',
            'validators errors' => $validate->messages(),
         ]);
    }
    $user = User::where('token','=',$req->token)->first();
    if($user->token!=null){
            if($user!= null){
                User::where('id',$user->id)->delete();

                return response()->json([
                    'status'=>'200',
                    'message'=>'User deleted succssfully',
                ]);
            }else{

                return response()->json([
                    'status'=>'405',
                    'message'=>'User not found',
                ]);
            }
    }else{
        return response()->json([
            'status'=>'405',
            'message' => 'login again',

            ]);

    }
}


    ////////////////////////////////////////

/*
    public function update_name(Request $request){////////work/////user


        $Validator = Validator::make($request->all(),[
            'name' =>'required|min:2|string|max:150',
            'token' =>'required',

        ]);

        $user = User::where('token','=',$request->token)->first();

        if($user != null){
            $user->name = $request->name;
            $user->update();
            return response()->json([
                    'name'=>$user->name,
                    'message'=>'user profile updated succssfully',
                 ],200);

        }else
        {
            return response()->json([

                'message'=>'user not exist',
             ],403);


        }
    }

*/

public function update_user_user(Request $request){////////work/////user


    $Validator = Validator::make($request->all(),[
        'name' =>'required|min:2|string|max:150',
        'date_of_birth'=>'required',
        'token' =>'required',

    ]);

    $user = User::where('token','=',$request->token)->first();

    if($user != null){
        $user->name = $request->name;
        $user->date_of_birth= $request->date_of_birth;
        if($request->hasFile('photo')){
            $file = $request->file('photo');
            $extension = $file->getClientOriginalExtension();
            $filename = time() . '.' . $extension;
            $file->move('uploads/users_images/',$filename);
            $user->profile_photo_path='uploads/users_images/' .$filename;
        }

        $user->update();
        return response()->json([
            'status'=>'200',
                'user'=>$user,
                'message'=>'user profile updated succssfully',
             ]);

    }else
    {
        return response()->json([
            'status'=>'405',
            'message'=>'user not exist',
         ]);


    }
}

// public function update_user_user(Request $request){////////work/////user


//     $Validator = Validator::make($request->all(),[
//         'name' =>'required|min:2|string|max:150',
//         'date_of_birth'=>'required',
//         'token' =>'required',
//         // 'photo'=>'image|mimes:jpg,bmp,png'

//     ]);

//     $user = User::where('token','=',$request->token)->first();

//     if($user != null){
//         $user->name = $request->name;
//         $user->date_of_birth= $request->date_of_birth;
//         if ($request->hasFile('photo')) {
//             echo "A7aa";
//         }
//         $user->profile_photo_path=$request->file('photo')->store('users');
//         $path = storage_path().'\app\\'.$user->profile_photo_path;
//         $user->update();
//         return response()->json([
//             'status'=>'200',
//                 'name'=>$user->name,
//                 'message'=>'user profile updated succssfully',
//              ]);

//     }else
//     {
//         return response()->json([
//             'status'=>'405',
//             'message'=>'user not exist',
//          ]);


//     }
// }




public function update_user(Request $request){////////////work///admin


    $Validator = Validator::make($request->all(),[
        'name' =>'required|min:2|string|max:150',
        'email' =>'required',
        'password' =>'nullable',
        'token' =>'required',
        'id' =>'required',
        'status'=>'required',
        'Is_Admin'=>'required',
        'date_of_birth'=>'required',
    ]);

    $admin = User::where('token','=',$request->token)->first();
    if($admin->Is_Admin==1){
        $user = User::where('id',$request->id);
        if($user != null){
            if($request->password!=null){
                $password=Hash::make($request->password);
                $user->update(['name'=>$request->name,'email'=>$request->email,'password'=>$password,'status'=>$request->status,'Is_Admin'=>$request->Is_Admin,  'date_of_birth'=> $request->date_of_birth]);
                return response()->json([
                    'status'=>'200',
                        'message'=>'user profile updated succssfully',
                    ]);
            }else{

                $user->update(['name'=>$request->name,'email'=>$request->email,'status'=>$request->status,'Is_Admin'=>$request->Is_Admin,  'date_of_birth'=> $request->date_of_birth]);
                return response()->json([
                    'status'=>'200',
                    'message'=>'user profile updated succssfully',
                    ]);


                }
        }else{
            return response()->json([
                'status'=>'405',
                'message'=>'user not exist',
            ]);


        }
    }else{
        return response()->json([
            'status'=>'407',
            'message'=>'out of your privileges',
         ]);
    }
}


    ////////////////////////////////////

    public function list_allUser(Request $request){//////////work

        $Validator = Validator::make($request->all(),[
            'token' =>'required',
        ]);
        $user = User::where('token','=',$request->token)->first();

        if($user->Is_Admin==1){
            $allUser = User::all();
            return response()->json([
                'status'=>'200',
                'allUser'=>$allUser,
                'message'=>'all users',
             ]);

        }else{
            return response()->json([
                'status'=>'407',
                'message'=>'out of your privileges',
             ]);
        }

    }

    public function list_new_five_Users(Request $request){//////////work

        $Validator = Validator::make($request->all(),[
            'token' =>'required',
        ]);
        $user = User::where('token','=',$request->token)->first();
        $allUser = User::all();
        $users=array();
        if($user->Is_Admin==1){
                $newusers = User::latest()->take(5)->get()->where('Is_Admin','=',$allUser->Is_Admin=0);

               // foreach($newusers as $id){
                    if($newusers!=null){

                       // $users[]=$newusers;
                        return response()->json([
                            'status'=>'200',
                            'newusers'=>$newusers,
                            'message'=>'new users',
                        ]);
                    }else{
                        return response()->json([
                            'status'=>'405',
                            'message'=>'not found new users',
                        ]);
               // }


                }

        }else{
            return response()->json([
                'status'=>'407',
                'message'=>'out of your privileges',
             ]);
        }

    }
/*
    public function active_user(Request $request){////////////////work
        $Validator = Validator::make($request->all(),[
            'token' =>'required',
            'id'=>'required',
        ]);
        $admin = User::where('token','=',$request->token)->first();
        if($admin->Is_Admin==1){
            $user = User::where('id',$request->id)->first();
            $user->update(['status'=>$user->status=1]);
            return response()->json([
                'user'=>$user,
                'message'=>' user actvaited successfully',
             ],200);

        }else{
            return response()->json([
                'message'=>'out of your privileges',
             ],200);
        }

    }


    public function suspend_user(Request $request){////////////work
        $Validator = Validator::make($request->all(),[
            'token' =>'required',
            'id'=>'required',
        ]);
        $admin = User::where('token','=',$request->token)->first();

        if($admin->Is_Admin==1){
            $user = User::where('id',$request->id)->first();

            $user->update(['status'=>$user->status=0]);
            return response()->json([
                'user'=>$user,
                'message'=>' user suspended successfully',
             ],200);

        }else{
            return response()->json([
                'message'=>'out of your privileges',
             ],200);
        }

    }


///////////////////////////////

    public function upgrade_user(Request $request){////////work
        $Validator = Validator::make($request->all(),[
            'token' =>'required',
            'id'=>'required',
        ]);
        $admin = User::where('token','=',$request->token)->first();

        if($admin->Is_Admin==1){
            $user = User::where('id',$request->id)->first();

            $user->update(['Is_Admin'=>$user->Is_Admin=1]);
            return response()->json([
                'user'=>$user,
                'message'=>' upgrade user successfully',
             ],200);

        }else{
            return response()->json([
                'message'=>'out of your privileges',
             ],200);
        }

    }

////////////////////////////////

public function add_admin(Request $request){////////work
    $Validator = Validator::make($request->all(),[
        'token' =>'required',
        'name'=>'required',
        'email'=>'required',
        'password'=>'required',
    ]);
    $admin = User::where('token','=',$request->token)->first();

    if($admin->Is_Admin==1){
        $user = new User;
        $user->name=$request->input('name');
        $user->email=$request->input('email');
        $user->password=Hash::make($request->input('password'));
        $user->Is_Admin = 1;
        $user->status = 1;
        $user->save();

        return response()->json([
            'user'=>$user,
            'message'=>' add new admin successfully',
         ],200);

    }else{
        return response()->json([
            'message'=>'out of your privileges',
         ],200);
    }

}



///////////////////////////////////////

    public function delete_admin(Request $request){/////////work
        $Validator = Validator::make($request->all(),[
            'token' =>'required',
            'id'=>'required',
        ]);
        $admin = User::where('token','=',$request->token)->first();

        if($admin->Is_Admin==1){
            $user = User::where('id',$request->id)->first();

            $user->update(['Is_Admin'=>$user->Is_Admin=0]);
            return response()->json([
                'user'=>$user,
                'message'=>' delete admin successfully',
             ],200);

        }else{
            return response()->json([
                'message'=>'out of your privileges',
             ],200);
        }

    }
*/
    ////////////////////////////////

    public function show_profile(Request $request){////////work
        $validate = Validator::make($request->all(),[
            'token'=>'required',
        ]);

        $user = User::where('token','=',$request->token)->first();
        $path=null;
        if($user->profile_photo_path!=null){
            $path = storage_path().'\app\\'.$user->profile_photo_path;
        }
        if($user->token!=null){
            return response()->json([
                'status'=>'200',
                'user' => $user,
                'photo'=>$path,
                'message' => 'profile detailes',
                ]);
        }else{
            return response()->json([
                'status'=>'405',
                'message' => 'login again',

                ]);

        }

    }

    public function get_user(Request $request){////////work
        $validate = Validator::make($request->all(),[
            'token'=>'required',
            'id'=>'required'
        ]);

        $admin = User::where('token','=',$request->token)->first();
        $user=new User;
        if($admin ->Is_Admin==1){
            
            $user = User::where('id','=',$request->id)->first();
        }
        if($user != null){
            return response()->json([
                'status'=>'200',
                'user' => $user,
                'message' => 'user detailes',
                ]);
        }else{
            return response()->json([
                'status'=>'405',
                'message' => 'user not found',

                ]);

        }

    }
}


