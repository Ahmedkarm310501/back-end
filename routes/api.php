<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\RegisterController;
use App\Http\Controllers\API\ProductController;

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
Route::post('register',[RegisterController::class,'register'] );

Route::post('login',[RegisterController::class,'login'] );



Route::middleware(['auth:sanctum'])->group(function(){
Route::post('logout',[RegisterController::class,'logout']);});

Route::post('add_product',[ProductController::class,'add_product'] );

Route::group(['middleware'=>'auth:sanctum'],function () {

    Route::get('get_profile', [RegisterController::class,'get_profile']);
    Route::post('profileUpdate',[RegisterController::class,'profileUpdate'] );

});


Route::delete('delete_user/{id}', [RegisterController::class,'delete_user']);


Route::delete('delete_product/{id}', [ProductController::class,'delete_product']);
