<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\support\facades\Hash;
use Illuminate\Support\Facades\Auth;
use File;
use Validator;

class ProductController extends Controller
{
    function add_product (Request $request){
        $validate = Validator::make($request->all(),[
            'id'=>'required',
            'name'=>'required',
            'price'=>'required',
            'Quantity'=>'required',
            'details'=>'required',
            'photo'=>'image|mimes:jpg,bmp,png',

        ]);
        if($validate->fails())
        {
            return response()->json([

                'validators errors' => $validate->messages(),
             ],200);

        }
        $pro=new Product;
        $pro->id=$request->input('id');
        $pro->name=$request->input('name');
        $pro->price=$request->input('price');
        $pro->Quantity=$request->input('Quantity');
        $pro->details=$request->input('details');
        $pro->photo=$request->file('photo')->store('products');

       $pro->save();
        return response()->json([

            'name'=>$pro->name,
            'message'=>'product added succssfully',
         ],200);

    }
    function delete_product($id){
        $product=Product::where('id',$id)->delete();

        if($product){
            return response()->json([
                'message'=>'product deleted succssfully',
             ],200);

        }
        return response()->json([


            'message'=>'product not found',
         ],200);


    }
}
