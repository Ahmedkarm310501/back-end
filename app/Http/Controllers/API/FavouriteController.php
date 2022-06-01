<?php

namespace App\Http\Controllers\API;
use Validator;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Favourite;
use App\Models\Product;
use App\Http\Controllers\Controller;

class FavouriteController extends Controller
{
    public function add_to_favourite(Request $request)
    {
        $validate= Validator::make($request ->all(),[
            'token'=>'required',
            'product_id'=>'required'

        ]);
    if($validate->fails())
    {
        return response()->json([
            'status'=>'403',
            'validators errors' => $validate->messages(),
         ]);
    }
    $user = User::where('token','=',$request->token)->first();
    $cart=Favourite::where('product_id',$request->product_id)->where('user_id',$user->id)->first();
    if($user && $cart==null)
    {
        $favourite = new Favourite;
        $favourite->user_id=$user->id;
        $favourite->product_id=$request->product_id;
        $favourite->save();
        return response()->json([
            'status'=>'200',
            'message' =>  'favourite added  successfully',
           ]);
    }else{
        return response()->json([
            'status'=>'405',
            'message' =>  'favourite already  exist',
           ]);
    }

    }
    public function delete_favourite(Request $request)
    {
        $validate= Validator::make($request ->all(),[
            'token'=>'required',
            'product_id'=>'required',

        ]);
    if($validate->fails())
    {
        return response()->json([
            'status'=>'403',
            'validators errors' => $validate->messages(),
         ]);
    }
    $user = User::where('token','=',$request->token)->first();
    $favourite=Favourite::where('product_id',$request->product_id)->where('user_id',$user->id)->first();
    if($favourite!=null)
    {
        Favourite::where('product_id',$request->product_id)->where('user_id',$user->id)->first()->delete();
        return response()->json([
            'status'=>'200',
            'message' =>  'favourite deleted successfully',
           ]);
    }
    else{
        return response()->json([
            'status'=>'405',
            'message' =>  'favourite not found',
           ]);
    }
    }
    public function get_all_favourite(Request $request)
    {
        $validate= Validator::make($request ->all(),[
            'token'=>'required',
        ]);
    if($validate->fails())
    {
        return response()->json([
            'validators errors' => $validate->messages(),
            'status'=>'403'
         ]);
    }
    $user = User::where('token','=',$request->token)->first();
    if($user!=null)
    {
        $post_ids = Favourite::where('user_id',$user->id)->pluck('product_id')->toArray();
        $pro = array();
    foreach($post_ids as $product_id){
    $product=Product::all()->where('id',$product_id)->first();
    if($product!=null)
    {
        $pro[]=$product;
    }else{
        return response()->json([
            'message' => 'product not found',
            'status'=>'405'
         ]);
    }

    }
    return response()->json([
        'products'=>$pro,
        'status'=>'200',
     ]);
    }else
    {
        return response()->json([
            'status'=>'405',
            'message'=>'user not found'
         ]);
    }

    }
}
