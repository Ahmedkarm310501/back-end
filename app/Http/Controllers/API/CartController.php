<?php

namespace App\Http\Controllers\API;
use Validator;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\User;
use App\Models\Product;
class CartController extends Controller
{
    public function add_to_cart(Request $request)
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
    $userCart= Cart::where('user_id','=',$user->id)->where('product_id','=',$request->product_id)->first();
    if($user)
    {
        if($userCart){
            //$newQuantity=$userCart->quantity+1;
            $userCart->update(['quantity'=>$userCart->quantity+1]);

        }else{
            $cart = new Cart;
            $cart->user_id=$user->id;
            $cart->product_id=$request->product_id;
            $cart->save();

        }
        return response()->json([
            'status'=>'200',
            'message' =>  'cart added  successfully',
           ]);
    }else{
        return response()->json([
            'status'=>'405',
            'message' =>  'user not found',
           ]);
    }


    }


    ////////////////////////////


    public function delete_all_cart(Request $request)
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
    $cart=Cart::where('product_id',$request->product_id)->where('user_id',$user->id)->first();
    if($cart!=null)
    {

        Cart::where('product_id',$request->product_id)->where('user_id',$user->id)->delete();
        return response()->json([
            'status'=>'200',
            'message' =>  'cart deleted successfully',
           ]);
    }
    else{
        return response()->json([
            'status'=>'405',
            'message' =>  'cart not found',
           ]);
    }
    }


    ///////////////////////////


    public function delete_one_cart(Request $request)
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
    $cart=Cart::where('product_id',$request->product_id)->where('user_id',$user->id)->first();
    if($cart!=null)
    {
        if($cart->quantity >1 ){
            $cart->update(['quantity'=>$cart->quantity-1]);
        }else{
            Cart::where('product_id',$request->product_id)->where('user_id',$user->id)->first()->delete();
        }

        return response()->json([
            'status'=>'200',
            'message' =>  'cart deleted successfully',
           ]);
    }
    else{
        return response()->json([
            'status'=>'405',
            'message' =>  'cart not found',
           ]);
    }
    }


    public function get_cart_total(Request $request)
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
            $post_ids = Cart::where('user_id',$user->id)->pluck('product_id')->toArray();
            $cart_ids = Cart::all()->where('user_id',$user->id);
            $pro = array();
            $total_price=0;
        foreach($post_ids as $product_id){
            $cart = Cart::where('product_id',$product_id)->where('user_id',$user->id)->first();

        $product=Product::all()->where('id',$product_id)->first();
        if($product!=null)
        {
            $total_price += $product->price *$cart->quantity;
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
            'cartID'=>$cart_ids,
            'total_price'=>$total_price,
            'status'=>'200'
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
