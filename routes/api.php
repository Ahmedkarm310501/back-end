<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\RegisterController;

use App\Http\Controllers\API\ContactFormController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\CategeryController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\CartController;
use App\Http\Controllers\API\AddressController;
use App\Http\Controllers\API\OrderController;
use App\Http\Controllers\API\VerificationController;
use App\Http\Controllers\API\FavouriteController;



/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

////////////////////////RegisterController//////////////////////
Route::group(['prefix'=>'v1'],function(){
    Route::post('register',[RegisterController::class,'register'] );
    Route::get('email/verify/{id}',[VerificationController::class,'verify'] )->name('verification.verify');
});

Route::post('check-email',[RegisterController::class,'check_email'] );
Route::post('check-username',[RegisterController::class,'check_username'] );

Route::post('login',[RegisterController::class,'login'] );

Route::post('logout',[RegisterController::class,'logout']);

Route::post('forget-pasword',[RegisterController::class,'forget_password'] );

Route::post('reset-pasword',[RegisterController::class,'reset_pasword'] );

//Route::middleware(['auth:sanctum'])->group(function(){});



////////////////////////ProductController////////////////////

Route::post('add-product',[ProductController::class,'add_product'] );

Route::post('update-product',[ProductController::class,'update_product'] );

Route::delete('delete-product', [ProductController::class,'delete_product']);

Route::get('all-products', [ProductController::class,'get_allProducts']);

Route::post('get-product', [ProductController::class,'get_product']);

Route::get('get-category', [ProductController::class,'get_products_of_category']);




///////////////////UserController////////////////
Route::post('add-user',[UserController::class,'addUser'] );////////for admin

Route::delete('delete-user', [UserController::class,'deleteUser']);///////for user to delete himself

//Route::post('update-name',[UserController::class,'update_name'] );///////for user

Route::post('update-user',[UserController::class,'update_user'] );//////for admin
Route::post('get-user',[UserController::class,'get_user'] );//////for admin



Route::post('list-allUser',[UserController::class,'list_allUser'] );////////for admin

Route::post('list-newUser',[UserController::class,'list_new_five_Users'] );////////for admin

Route::post('update_user_user',[UserController::class,'update_user_user'] );///////for user


//Route::post('active-user',[UserController::class,'active_user'] );////////for admin

//Route::post('suspend-user',[UserController::class,'suspend_user'] );////////for admin

//Route::post('upgrade-user',[UserController::class,'upgrade_user'] );////////for admin

//Route::post('add-admin',[UserController::class,'add_admin'] );////////for admin

//Route::post('delete-admin',[UserController::class,'delete_admin'] );////////for admin

Route::post('show-profile',[UserController::class,'show_profile'] );//////for user


/////////////////////////////ContactFormController//////////

Route::post('contact', [ContactFormController::class, 'ContactForm']);


///////////////////////CategoryController////////////

Route::post('add-category', [CategeryController::class, 'add_category']);

Route::post('update-category', [CategeryController::class, 'update_category']);

Route::delete('delete-category', [CategeryController::class, 'delete_category']);

Route::post('category_names', [CategeryController::class, 'category_names']);




/////////////////////////////////CartController/////////////////

Route::post('add_to_cart', [CartController::class, 'add_to_cart']);

Route::delete('delete_all_cart', [CartController::class, 'delete_all_cart']);

Route::delete('delete_one_cart', [CartController::class, 'delete_one_cart']);

Route::post('get_cart_total', [CartController::class, 'get_cart_total']);


///////////////////////////////Adressess ///////////////////////////////

Route::post('create_address', [AddressController::class, 'create_address']);

Route::delete('delete_address', [AddressController::class, 'delete_address']);

Route::post('get_all_users_address', [AddressController::class, 'get_all_users_address']);



//////////////////////OrderController///////////////
Route::post('add-order', [OrderController::class, 'add_order']);

Route::post('get-order', [OrderController::class, 'get_allorders']);

Route::delete('cancel-order', [OrderController::class, 'cancel_order']);
Route::post('get-orders', [OrderController::class, 'get_orders']);
Route::post('get-newOrders', [OrderController::class, 'list_new_seven_Orders']);
Route::post('get_order_user', [OrderController::class, 'get_order_user']);
Route::post('activate_order', [OrderController::class, 'activate_order']);
Route::post('get_statistics', [OrderController::class, 'get_statistics']);
Route::post('get_charts', [OrderController::class, 'get_charts']);

Route::post('check_out', [OrderController::class, 'check_out']);

/////////////////////////FavouriteController///////////////////

Route::post('add_to_favourite', [FavouriteController::class, 'add_to_favourite']);

Route::delete('delete_favourite', [FavouriteController::class, 'delete_favourite']);

Route::post('get_all_favourite', [FavouriteController::class, 'get_all_favourite']);

