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

Route::post('profileUpdate',[RegisterController::class,'profileUpdate'] )->middleware('auth:sanctum');

Route::middleware(['auth:sanctum'])->group(function(){
Route::post('logout',[RegisterController::class,'logout']);});

Route::post('add_product',[ProductController::class,'add_product'] );

Route::delete('delete_product/{id}', [ProductController::class,'delete_product']);
