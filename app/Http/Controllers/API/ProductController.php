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
       // $pro->id=$request->input('id');
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
    ////////////////////////////////
    ////////////////////////////////
    function update_product($id ,Request $req){

        $Validator = Validator::make($req->all(),[

            'name'=>'required',
            'price'=>'required',
            'Quantity'=>'required',
            'details'=>'required',
            'photo'=>'image|mimes:jpg,bmp,png|nullable',
              ]);
       $product =Product::find($id);

        if($product){
        $product->name=$req->name;
        $product->price=$req->price;
        $product->Quantity=$req->Quantity;
        $product->details=$req->details;
        if($req->hasFile('photo')){
            if($product->photo){
                $old_path=public_path().'C:\Users\lap\Downloads\photos'.$product->photo;
                if(File::exists($old_path)){
                    File::delete($old_path);
                }
            }

            $image_name='image-'.time().'.'.$req->photo->extension();
            $req->photo->move(public_path('C:\Users\lap\Downloads\photos'),$image_name);
        }else{
            $image_name=$product->photo;

        }
        $product->update();
        return response()->json([

            'name'=>$product->name,
            'message'=>'product updated succssfully',
         ],200);
    }

        return response()->json([


            'message'=>'product not found ',
         ],200);
    }


    //////////////////////////////////
    /////////////////////////////////
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
/////////////////////////////////////////
////////////////////////////////////////

     function get_All_products(){

        $All_products=Product::all();
        return response()->json([
            'products'=>$All_products,
        ],200);

    }

///////////////////////////////////////
//////////////////////////////////////

      function get_product(Request $request) {

        // $All_products=Product::all();
         $product =Product::find($request->id)->get();
         //if($product){
            return response()->json([
                'products'=>$product,
             ],200);

       //  }
        // return response()->json([
          //  'message'=>'product not found',
        // ],200);
   }
 }

