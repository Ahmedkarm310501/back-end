<?php

namespace App\Http\Controllers\API;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\User;
use App\Models\Order;
use App\Models\Cart;
use App\Models\Address;
use Illuminate\Support\Str;
use Illuminate\support\facades\Hash;
use Illuminate\Support\Facades\Auth;
use File;
use Validator;
use \stdClass;
use DateTime;
use DB;


class OrderController extends Controller
{

public function get_allorders(Request $request){
    $validate= Validator::make($request ->all(),[
        'token'=>'required',
        'order_id' => 'required',

    ]);
    if($validate->fails())
    {
        return response()->json([
            'status'=>'403',
            'validators errors' => $validate->messages(),
         ]);

    }
    $user = User::where('token','=',$request->token)->first();
    $address=Address::where('user_id',$user->id)->first();


        if($user){
            $order = Order::all()->where('id','=',$request->order_id)->where('user_id',$user->id)->where('address_id','=',$address->user_id);
            if($order){
                return response()->json([
                    'status'=>'200',
                    "data"=>$order,
                    'message' =>'order details'
                ]);
            }else{
                return response()->json([
                    'status'=>'403',
                    'message' =>'you dont have order'
                ]);

            }
        }else{
            return response()->json([
                'status'=>'405',
                'message' =>'please create email to make your order'
            ]);

        }


}
public function add_order(Request $request){
    $validate= Validator::make($request ->all(),[
        'token'=>'required',
        'products' => 'required',
        'address_id'=>'required',
       "delivery_type"=>'required',
    ]);
    if($validate->fails())
    {
        return response()->json([
            'status'=>'403',
            'validators errors' => $validate->messages(),
         ]);

    }
    $user = User::where('token','=',$request->token)->first();
    $address = Address::where('id','=',$request->address_id)->first();
        if($user){

            $order = new Order;
            $product_and_quantity=array();
            $counter=0;
            foreach($request->products as $product_id){
                $cart = Cart::where('product_id',$product_id)->where('user_id',$user->id)->first();
                $product=Product::all()->where('id',$product_id)->first();
                $product_and_quantity[$counter]=['id'=>$product->id,'quantity'=>$cart->quantity];
                $counter++;
             }
            $order->products = json_encode($product_and_quantity);
            $order->user_id = $user->id;
            $order->delivery_type = $request->delivery_type;
            if($user->id == $address->user_id)
            {
                $order->address_id = $request->address_id;
            }else{
                return response()->json([
                    'status'=>'406',
                    'message' =>'please make address for this user'
                ]);
            }
            $total_price=0;
            foreach($request->products as $product_id){

                $cart = Cart::where('product_id',$product_id)->where('user_id',$user->id)->first();

                $product=Product::all()->where('id',$product_id)->first();
                if($product!=null && $product->Quantity >= $cart->quantity)
                {
                    $total_price += $product->price *$cart->quantity;
                    $product->update(['Quantity'=>$product->Quantity - $cart->quantity]);

                }else{
                    return response()->json([
                        'status'=>'407',
                        'message' =>'this product does not have enough quantity'
                    ]);
                }

            }

            if($order->delivery_type==1){

                $total_price+=50;
            }
            $order->total_price = $total_price;
            $order->save();
            Cart::where('user_id',$user->id)->delete();
            return response()->json([
                'status'=>'200',
                'order'   => $order,
                'message' => $order ? 'Order Created successfully' : 'Error Creating Order'
            ]);



        }else{
            return response()->json([
                'status'=>'405',
                'message' =>'please create email to make your order'
            ]);

        }

}


// public function add_order(Request $request){
//     $validate= Validator::make($request ->all(),[
//         'token'=>'required',
//         'products' => 'required',
//         'address_id'=>'required',
//        "delivery_type"=>'required',
//     ]);
//     if($validate->fails())
//     {
//         return response()->json([
//             'status'=>'403',
//             'validators errors' => $validate->messages(),
//          ]);

//     }
//     $user = User::where('token','=',$request->token)->first();
//     $address = Address::where('id','=',$request->address_id)->first();
//         if($user){

//             $order = new Order;
//             $order->products = json_encode($request->products);
//             $order->user_id = $user->id;
//             $order->delivery_type = $request->delivery_type;
//             if($user->id == $address->user_id)
//             {
//                 $order->address_id = $request->address_id;
//             }else{
//                 return response()->json([
//                     'status'=>'406',
//                     'message' =>'please make address for this user'
//                 ]);
//             }
//             $total_price=0;
//             foreach($request->products as $product_id){

//                 $cart = Cart::where('product_id',$product_id)->first();

//                 $product=Product::all()->where('id',$product_id)->first();
//                 if($product!=null)
//                 {
//                     $total_price += $product->price *$cart->quantity;

//                 }

//             }

//             if($order->delivery_type==1){

//                 $total_price+=50;
//             }
//             $order->total_price = $total_price;
//             $order->save();
//             Cart::where('user_id',$user->id)->delete();
//             return response()->json([
//                 'status'=>'200',
//                 'order'   => $order,
//                 'message' => $order ? 'Order Created successfully' : 'Error Creating Order'
//             ]);



//         }else{
//             return response()->json([
//                 'status'=>'405',
//                 'message' =>'please create email to make your order'
//             ]);

//         }

// }


    public function cancel_order(Request $request){
        $validate= Validator::make($request ->all(),[
            'token'=>'required',
            'order_id' => 'required',

        ]);
        if($validate->fails())
        {
            return response()->json([
                'status'=>'403',
                'validators errors' => $validate->messages(),
             ]);

        }
        $user = User::where('token','=',$request->token)->first();
        $address=Address::where('user_id',$user->id)->first();
            if($user){
                $order = Order::all()->where('id','=',$request->order_id)->where('user_id',$user->id)->where('address_id','=',$address->user_id);
                if($order){
                    Order::where('id','=',$request->order_id)->where('user_id',$user->id)->where('address_id','=',$address->user_id)->delete();

                    return response()->json([
                        'status'=>'200',
                        'message' =>'order canceled successfully'
                    ]);
                }else{
                    return response()->json([
                        'status'=>'403',
                        'message' =>'you dont have order to cancel'
                    ]);

                }
            }else{
                return response()->json([
                    'status'=>'405',
                    'message' =>'please create email to make your order'
                ]);

            }


    }


    public function update_order(Request $request){
        $validate= Validator::make($request ->all(),[
            'token'=>'required',
            'products' => 'required',
            'address_id'=>'required',
            "total_price"=>'required',
            "status"=>'required',
        ]);
        if($validate->fails())
        {
            return response()->json([
                'status'=>'403',
                'validators errors' => $validate->messages(),
             ],200);

        }
        $user = User::where('token','=',$request->token)->first();
        $address = Address::where('id','=',$request->address_id)->first();
            if($user){
                Order::where('id','=',$request->order_id)->where('user_id',$user->id)->where('address_id','=',$address->user_id)->delete();
                $order = new Order;
                $order->products=json_encode($request->products);
                $order->user_id=$user->id;
                $order->address_id=$request->address_id;
                $order->total_price=$request->total_price;
                $order->save();
                return response()->json([
                    'status'=>'200',
                    'data'   => $order,
                    'message' => $order ? 'Order Created successfully' : 'Error Creating Order'
                ]);
            }else{
                return response()->json([
                    'status'=>'405',
                    'message' =>'please create email to make your order'
                ]);

            }

    }


    public function list_new_seven_Orders(Request $request){//////////work

        $Validator = Validator::make($request->all(),[
            'token' =>'required',
        ]);
        $user = User::where('token','=',$request->token)->first();
        if($user->Is_Admin==1){
                $neworders = Order::latest()->take(7)->get();
                $order_array = array();

                foreach($neworders as $id)
                {

                    $order_l = Order::where('id',$id->id)->first();
                    $user_a = User::where('id',$order_l->user_id)->first();
                    $obj = new stdClass();
                    $obj->user_id = $user_a->id;
                    $obj->user_photo = $user_a->profile_photo_path;
                    $obj->email = $user_a->email;
                    $obj->name = $user_a->name;
                    $obj->order_id = $id->id;
                    $obj->date = $id->created_at->toDateString();
                    $obj->status = $order_l->status;
                    $obj->total_price = $order_l->total_price;
                    $order_array[] = $obj;
                }
                return response()->json([
                    'status'=>'200',
                    "orders_array"=>$order_array,
                ]);

        }else{
            return response()->json([
                'status'=>'407',
                'message'=>'out of your privileges',
             ]);
        }

    }

    public function activate_order(Request $request)
    {
        $validate = Validator::make($request->all(),[
            'token' =>'required',
            'order_id'=>'required'
        ]);
        if($validate->fails())
        {
            return response()->json([
                'status'=>'403',
                'validators errors' => $validate->messages(),
             ],200);

        }
        $user = User::where('token','=',$request->token)->first();
        if($user->Is_Admin==1)
        {
            $order=Order::where('id',$request->order_id)->first();
            if($order->status==0)
            {
                $order->status=1;
                $order->update();
                return response()->json([
                    'status'=>'200',
                    'message'=>'order activated successfully',
                ]);
            }
            else{
                return response()->json([
                    'status'=>'405',
                    'message'=>'order already activated',
                ]);
            }
        }else{
            return response()->json([
                'status'=>'403',
                'message'=>'user not admin',
            ]);
        }

    }


    public function get_statistics(Request $request)
    {
        $validate = Validator::make($request->all(),[
            'token' =>'required',
        ]);
        if($validate->fails())
        {
            return response()->json([
                'status'=>'403',
                'validators errors' => $validate->messages(),
             ],200);

        }
        $user = User::where('token','=',$request->token)->first();
        $total_pending=0;
        $total_sales=0;
        $total=0;
        if($user->Is_Admin==1){
            $order_ids_pending = Order::where('status','=',0)->pluck('id')->toArray();
            $order_ids_sales = Order::where('status','=',1)->pluck('id')->toArray();
            if($order_ids_pending!=null)
            {
                foreach($order_ids_pending as $order_id){
                    $order=Order::all()->where('id',$order_id)->first();
                    $total_pending += $order->total_price;
                }
            }else{
                $total_pending=0;
            }
            if($order_ids_sales!=null)
            {
                foreach($order_ids_sales as $order_id){
                    $order=Order::all()->where('id',$order_id)->first();
                    $total_sales += $order->total_price;
                }
            }else{
                $total_sales=0;
            }
            $total = $total_sales + $total_pending;
            return response()->json([
                'status'=>'200',
                'total_pending'=>$total_pending,
                'total_sales'=>$total_sales,
                'total' => $total
            ]);
        }else{
            return response()->json([
                'status'=>'408',
                'message'=>'user not admin',
            ]);
        }
    }

    public function get_charts(Request $request)
    {
        $validate = Validator::make($request->all(),[
            'token' =>'required',
        ]);
        if($validate->fails())
        {
            return response()->json([
                'status'=>'403',
                'validators errors' => $validate->messages(),
             ],200);

        }
        $user = User::where('token','=',$request->token)->first();
        $total_pending=0;
        $total_sales=0;
        $total=0;
        if($user->Is_Admin==1){
            $order_ids_pending = Order::where('status','=',0)->pluck('id')->toArray();
            $order_ids_sales = Order::where('status','=',1)->pluck('id')->toArray();
            if($order_ids_pending!=null)
            {
                foreach($order_ids_pending as $order_id){
                    $order=Order::all()->where('id',$order_id)->first();
                    $total_pending += $order->total_price;
                }
            }else{
                $total_pending=0;
            }
            if($order_ids_sales!=null)
            {
                foreach($order_ids_sales as $order_id){
                    $order=Order::all()->where('id',$order_id)->first();
                    $total_sales += $order->total_price;
                }
            }else{
                $total_sales=0;
            }
            ////////////////////chart-one/////////////////////////////
            $order_ids = Order::pluck('id')->toArray();
            $chart_one = array();
            foreach($order_ids as $order_id){
                $order=Order::where('id',$order_id)->first();
                $date = $order->created_at;
                $date    = new DateTime($date);
                $day = $date->format('l');
                $object = new stdClass();
                $d = $order->created_at->toDateString();
                $orders = Order::select(DB::raw('sum(total_price) as sums'),)->whereDate('created_at', date($d))->first();
                $object->name = $day;
                $object->total_price=$orders->sums;
                $chart_one[]=$object;
            }
            $chart_new=array();
            $chart_new=array_unique($chart_one,SORT_REGULAR);
            //////////////////////////////////////////////////////////////
            ///////////////////chart-two//////////////////////////////////
            $user_ids = User::pluck('id')->toArray();
            $active_user=0;
            $inactive_user=0;
            foreach($user_ids as $user_id){
                $user=User::where('id',$user_id)->first();
                if($user->status==1)
                {
                    $active_user++;
                }else{
                    $inactive_user++;
                }
            }
            $object_active = new stdClass();
            $object_active->name='active';
            $object_active->numberOfActiveUsers=$active_user;
            $object_inactive = new stdClass();
            $object_inactive->name='inactive';
            $object_inactive->numberOfActiveUsers=$inactive_user;
            $chart_two=array();
            $chart_two[]=$object_active;
            $chart_two[]=$object_inactive;
            //////////////////////////////////////////////////////////////
            //////////////////////chart-three/////////////////////////////
            $products_ids = Product::pluck('id')->toArray();

            $chart_three=array();

            for($i=0 ; $i < sizeof($products_ids) ; $i++)
            {
                $count=0;
                $pp=Product::where('id',$products_ids[$i])->first();
                for($j=0 ;$j<sizeof($order_ids);$j++ )
                {
                    $order=Order::where('id',$order_ids[$j])->first();
                    $integerIDs = array_map('intval', json_decode($order->products, true));
                    if(in_array($products_ids[$i], $integerIDs))
                    {
                        $count++;
                    }
                }
                $product_sale = new stdClass();
                $product_sale->name=$pp->name;
                $product_sale->NumberOfProductsSales=$count;
                $chart_three[]=$product_sale;
            }
            ///////////////////////////////////////////////////////////////////
            $total = $total_sales + $total_pending;
            return response()->json([
                'status'=>'200',
                'total_pending'=>$total_pending,
                'total_sales'=>$total_sales,
                'total' => $total,
                'chart_one'=>$chart_new,
                'chart_two'=>$chart_two,
                'chart_three'=>$chart_three
            ]);
        }else{
            return response()->json([
                'status'=>'408',
                'message'=>'user not admin',
            ]);
        }
    }

    public function check_out(Request $request){
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
        $addresses=Address::all()->where('user_id',$user->id);

        if($addresses!=null){

            $post_ids = Cart::where('user_id',$user->id)->pluck('product_id')->toArray();
            $total_price=0;
            foreach($post_ids as $product_id){
                $cart = Cart::where('product_id',$product_id)->where('user_id',$user->id)->first();
                $product=Product::all()->where('id',$product_id)->first();
                    if($product!=null){
                        $total_price += $product->price *$cart->quantity;

                    }
                }
                return response()->json([
                    'status'=>'200',
                    'productIDs'=>$post_ids,
                    'addresses'=>$addresses,
                    'total_price'=>$total_price,
                   ]);
        }else{
            return response()->json([
                'status'=>'405',
                'message' =>  'no addresses for this user',
            ]);


        }
    }




    public function get_orders(Request $request)
{
    $validate= Validator::make($request ->all(),[
        'token'=>'required',
    ]);
    if($validate->fails())
    {
        return response()->json([
            'status'=>'406',
            'validators errors' => $validate->messages(),
         ]);

    }
    $user = User::where('token','=',$request->token)->first();
    $user_id= $user->id;
    if($user)
    { if($user->Is_Admin)
        {
            $order_array = array();
            $order_ids = Order::all()->pluck('id')->toArray();
            foreach($order_ids as $id)
            {

                $order_l = Order::where('id',$id)->first();
                $user_a = User::where('id',$order_l->user_id)->first();
                $obj = new stdClass();
                $obj->user_id = $user_a->id;
                $obj->user_photo = $user_a->profile_photo_path;
                $obj->email_user = $user_a->email;
                $obj->id = $id;
                $obj->username=$user_a->name;
                $obj->status = $order_l->status;
                $obj->total_price = $order_l->total_price;
                $order_array[] = $obj;
            }
            return response()->json([
                'status'=>'200',
                "orders_array"=>$order_array,
            ]);
    }else{
        return response()->json([
            'status'=>'403',
            'message' =>'user not admin'
        ]);}
    }else{
        return response()->json([
            'status'=>'406',
            'message' =>'user not found'
        ]);
    }
}
public function get_order_user(Request $request)
{
    $validate= Validator::make($request ->all(),[
        'token'=>'required',
    ]);
    if($validate->fails())
    {
        return response()->json([
            'status'=>'406',
            'validators errors' => $validate->messages(),
         ]);

    }
    $user = User::where('token','=',$request->token)->first();
    $user_id= $user->id;
    if($user)
    {
            $order_array = array();
            $order_ids = Order::where('user_id',$user_id)->pluck('id')->toArray();
            foreach($order_ids as $id)
            {
                $order_l = Order::where('id',$id)->first();
                $productsIDs =json_decode($order_l->products);
                for($i=0; $i<sizeof($productsIDs);$i++)
                {
                    $obj = new stdClass();
                    $obj->order_id =$id;
                    $prod = Product::where('id',$productsIDs[$i]->id)->first();
                    $obj->product_name =$prod->name;
                    $obj->product_id =$prod->id;
                    $obj->photo = $prod->photo;
                    $obj->status = $order_l ->status;
                    $obj->Quantity= $productsIDs[$i]->quantity;
                    $obj->total_price = $productsIDs[$i]->quantity * ($prod->price);
                    $order_array[] = $obj;
                }
            }
            return response()->json([
                'status'=>'200',
                "orders_array"=>$order_array,
            ]);
    }else{
        return response()->json([
            'status'=>'403',
            'message' =>'user not found'
        ]);
    }

}

}
