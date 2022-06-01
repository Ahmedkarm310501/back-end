<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Categery;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\support\facades\Hash;
use Illuminate\Support\Facades\Auth;
use File;
use Validator;

class ProductController extends Controller
{
    public function add_product (Request $request){
        $validate = Validator::make($request->all(),[
            'token'=>'required',
            'name'=>'required',
            'price'=>'required',
            'Quantity'=>'required',
            'details'=>'required',
            'photo'=>'required||image|mimes:jpg,bmp,png',
            'category_name'=>'required',


        ]);
        if($validate->fails())
        {
            return response()->json([
                'status'=>'403',
                'validators errors' => $validate->messages(),
             ]);

        }
        $user = User::where('token','=',$request->token)->first();
        $cat = categery::where('name','=',$request->category_name)->first();

            if($user){
                if($user->Is_Admin==1){
                    $pro=new Product;
                    $pro->name=$request->input('name');
                    $pro->price=$request->input('price');
                    $pro->Quantity=$request->input('Quantity');
                    $pro->details=$request->input('details');
                    if($request->hasFile('photo')){
                        $file = $request->file('photo');
                        $extension = $file->getClientOriginalExtension();
                        $filename = time() . '.' . $extension;
                        $file->move('uploads/product_images/',$filename);
                        $pro->photo='uploads/product_images/' .$filename;

                    }
                    $pro->category_id=$cat->id;
                    $pro->save();
                    return response()->json([
                        'status'=>'200',
                        'message'=>'product added succssfully',
                    ]);
                }else{
                    return response()->json([
                        'status'=>'407',
                    'message'=>'out of your privileges',
                ]);
                }

            }else{
                return response()->json([
                    'status'=>'405',

                'message'=>'user not found',
            ]);
        }
    }
////////////////////////////////////////

   public  function get_allProducts(Request $request){

        $All_products=Product::all();


        if($All_products!=null){
            return response()->json([
                'status'=>'200',
                'products'=>$All_products,
                'message'=>'all products'
            ]);
        }else{
            return response()->json([
                'status'=>'405',
                'message'=>'not found products to show',
                ]);
        }
    }

///////////////////////////////////////
//////////////////////////////////////

    public  function get_product(Request $request) {
        $Validator = Validator::make($request->all(),[
            //'token'=>'required',
            'id'=>'required,'
              ]);
        $product = Product::where('id','=',$request->id)->first();
        
        if($product!=null){
            $catrgory= Categery::where('id','=',$product->category_id)->first();
            return response()->json([
                'status'=>'200',
                'products'=>$product,
                'category'=>$catrgory,
                'message'=>'product',
            ]);
        }else{
            return response()->json([
                'status'=>'405',
                'message'=>'not found to show',
            ]);

        }
   }



   public  function get_products_of_category(Request $request){
    $Validator = Validator::make($request->all(),[
        'category_id'=>'required,'
          ]);
    $All_products=Product::all()->where('category_id','=',$request->category_id);

        if($All_products!=null){
            return response()->json([
                'status'=>'200',
                'products'=>$All_products,
                'message'=>'all products'
            ]);
        }else{
            return response()->json([
                'status'=>'405',
                'message'=>'not found products to show',
            ]);

        }

}

public function update_product (Request $request){
    $validate = Validator::make($request->all(),[
        'token'=>'required',
        'id'=>'required',
        'name'=>'required',
        'price'=>'required',
        'Quantity'=>'required',
        'details'=>'required',
        'photo'=>'image|mimes:jpg,bmp,png',
        'category_name'=>'required',


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

                $cat = Categery::where('name','=',$request->category_name)->first();
                $pro = Product::where('id','=',$request->id)->first();
                if($request->hasFile('photo')){
                    $file = $request->file('photo');
                    $extension = $file->getClientOriginalExtension();
                    $filename = time() . '.' . $extension;
                    $file->move('uploads/product_images/',$filename);
                    $pro->photo='uploads/product_images/' .$filename;
                    $pro ->update([
                    'name'=>$request->name,
                    'price'=>$request->price,
                    'Quantity'=>$request->Quantity,
                    'details'=>$request->details,
                    'category_id'=>$cat->id
                    ]);
                    return response()->json([
                        'status'=>'200',

                        'message'=>'product updated successfully',
                    ]);

                }else{
                    $pro ->update([
                        'name'=>$request->name,
                        'price'=>$request->price,
                        'Quantity'=>$request->Quantity,
                        'details'=>$request->details,
                        'category_id'=>$cat->id
                        ]);
                    return response()->json([
                        'status'=>'200',
                        'name'=>$pro->name,
                        'message'=>'product updated successfully',
                    ]);
                }


            }else{
                return response()->json([
                    'status'=>'407',
                'message'=>'out of your privileges',
            ]);
            }

        }else{
            return response()->json([
                'status'=>'405',

            'message'=>'user not found',
        ]);
    }
}

public  function delete_product(Request $request) {
    $Validator = Validator::make($request->all(),[
        'token'=>'required',
        'id'=>'required,'
          ]);
    $user = User::where('token','=',$request->token)->first();
    $product = Product::where('id','=',$request->id)->first();
    if($user->Is_Admin==1){
        if($product!=null){
            Product::where('id','=',$request->id)->first()->delete();
            return response()->json([
                'status'=>'200',
                'message'=>'product deleted successfully ',


            ]);
        }else{
            return response()->json([
                'status'=>'405',
                'message'=>'not found to show',
            ]);

        }
    }else{
        return response()->json([
            'status'=>'407',
        'message'=>'out of your privileges',
    ]);
    }

    

}



 }

