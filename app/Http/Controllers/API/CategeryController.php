<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Product;
use App\Models\Categery;
use Illuminate\Support\Str;
use Illuminate\support\facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\forgetPassword;

use Validator;

class CategeryController extends Controller

{
    public function add_category(Request $request){
        $validate= Validator::make($request ->all(),[
            'name'=>'required|max:150|min:2',
            'token'=>'required',
        ]);
        if($validate->fails())
        {
            return response()->json([
                'status'=>'403',
                'validators errors' => $validate->messages(),
             ]);

        }
        $user = User::where('token','=',$request->token)->first();
            if($user){
                if($user->Is_Admin==1){
                    $category = new Categery;
                    $category->name = $request->input('name');
                    $category->save();
                    return response()->json([
                        'status'=>'200',
                        'name'=>$category,
                        'message'=>'category added succssfully',
                    ]);

                }else{
                    return response()->json([
                    'status'=>'407',
                    'message'=>'out of your privileges',
                ]);}
            }else{
                return response()->json([

                    'status'=>'405',
                'message'=>'user not found',
            ]);}

    }



///////////////////////////////////////////


public function update_category(Request $request){
    $validate= Validator::make($request ->all(),[
        'name'=>'required|max:150|min:2',
        'token'=>'required',
        'id'=>'required',
    ]);
    if($validate->fails())
    {
        return response()->json([
            'status'=>'403',
            'validators errors' => $validate->messages(),
         ]);

    }
    $user = User::where('token','=',$request->token)->first();
        if($user){
            if($user->Is_Admin==1){
                $category = Categery::where('id','=',$request->id)->first();
                if($category!=null){
                    $category->update(['name'=>$request->name]);
                    return response()->json([
                    'status'=>'200',
                    'name'=>$category,
                    'message'=>'category updated succssfully',
                    ]);
                }else{
                    return response()->json([
                        'status'=>'405',
                    'message'=>'category not found',
                    ]);
                }
            }else{
                return response()->json([
                    'status'=>'407',
                'message'=>'out of your privileges',
            ]);}
        }else{
            return response()->json([
                'status'=>'405',

            'message'=>'user not found',
        ]);}

}


//////////////////////////

public function delete_category(Request $request){
    $validate= Validator::make($request ->all(),[
        'token'=>'required',
        'id'=>'required',
    ]);
    if($validate->fails())
    {
        return response()->json([
            'status'=>'403',
            'validators errors' => $validate->messages(),
         ]);

    }
    $user = User::where('token','=',$request->token)->first();
        if($user){
            if($user->Is_Admin==1){
                $category = Categery::where('id','=',$request->id)->first();
                if($category!=null){
                    Categery::where('id',$category->id)->delete();

                    return response()->json([
                        'status'=>'200',
                    'message'=>'category deleted succssfully',
                    ]);
                }else{
                    return response()->json([
                        'status'=>'405',
                    'message'=>'category not found',
                    ]);
                }
            }else{
                return response()->json([
                    'status'=>'407',
                'message'=>'out of your privileges',
            ]);}
        }else{
            return response()->json([

                'status'=>'405',
            'message'=>'user not found',
        ]);}

}

public function category_names(Request $request)
{
    $validate= Validator::make($request ->all(),[
        'token'=>'required',
    ]);
    if($validate->fails())
    {
        return response()->json([
            'status'=>'403',
            'validators errors' => $validate->messages(),
         ]);

    }
    $user = User::where('token','=',$request->token)->first();
            if($user){
                if($user->Is_Admin==1){
                    $categeries_ids = Categery::pluck('id')->toArray();
                    $categeries_names = array();
                    foreach($categeries_ids as $id)
                    {
                        $ca = Categery::where('id',$id)->first();
                        $categeries_names[] = $ca->name;
                    } 
                    return response()->json([
                        'status'=>'200',
                        'categeries_names'=>$categeries_names,
                    ]);
                }else{
                    return response()->json([
                    'status'=>'407',
                    'message'=>'this user not admin',
                ]);}
            }else{
                return response()->json([

                    'status'=>'405',
                'message'=>'user not found',
            ]);}

}

}
