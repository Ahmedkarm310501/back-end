<?php

namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use App\Models\Address;
use App\Models\User;
class AddressController extends Controller
{
public function create_Address(Request $request)
{
    $validate= Validator::make($request ->all(),[
        'name'=>'required|max:150|min:2',
        'address' => 'required',
        'phone'=>'required',
        'city'=>'required',
        'type'=>'required',
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
if($user)
{
    $address= new Address;
    $address->name=$request->name;
    $address->address=$request->address;
    $address->phone=$request->phone;
    $address->city=$request->city;
    $address->type=$request->type;
    $address->user_id=$user->id;
    $address->save();
    return response()->json([
        'status'=>'200',
      'message' =>  'address created successfully',
     ]);

}
else{
    return response()->json([
        'status'=>'405',
        'message' =>  'user not found',
       ]);
}
}
public function delete_address(Request $request)
{
    $validate= Validator::make($request ->all(),[
        'id'=>'required',
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
    $address=Address::where('id',$request->id)->where('user_id',$user->id)->first();

    if($address!=null)
    {
        Address::where('id',$request->id)->delete();
        return response()->json([
            'status'=>'200',
            'message' =>  'address deleted successfully',
           ]);

    }
    else{
        return response()->json([
            'status'=>'405',
            'message' =>  'user or address not found',
           ]);
    }

}
public function get_all_users_address(Request $request)
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
    $address=Address::all()->where('user_id',$user->id);
    if($address!=null)
    {
        return response()->json([
            'status'=>'200',
            'addresses'=>$address
           ]);

    }
    else{
        return response()->json([
            'status'=>'405',
            'message' =>  'no addresses for this user',
           ]);
    }


}

}
