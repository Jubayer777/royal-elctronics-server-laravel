<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

    

Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);
Route::post('/verify-user', [AuthController::class, 'verifyUser']);

Route::group(['middleware'=>'api','prefix'=>'auth'],function($router){
    Route:: post('/register',[AuthController::class, 'register']);
    Route:: post('/login',[AuthController::class, 'login']);
    Route:: get('/profile',[AuthController::class, 'profile']);
    Route:: post('/logout',[AuthController::class, 'logout']);
});

Route::group(['prefix'=>'p1'],function($router){
    Route::get('/products',[ProductController::class, 'index']);
    Route::get('/products/latest',[ProductController::class, 'latest']); 
    Route::get('/products/{product}',[ProductController::class, 'show']);
    Route::get('/products/search/{name}',[ProductController::class, 'search']);
    
    Route::group(['middleware'=>'auth'],function($router){
        Route::post('/products',[ProductController::class, 'store']);
        Route::delete('/products/{product}',[ProductController::class, 'destroy']);
        Route::patch('/products/{product}',[ProductController::class, 'update']);
    });
    
});


    Route::group(['prefix'=>'c1'],function($router){
        Route::get('/categories',[CategoryController::class, 'index']);
        Route::get('/categories/{category}',[CategoryController::class, 'productsByCategory']);
        
        Route::group(['middleware'=>'auth'],function($router){
            Route::post('/categories',[CategoryController::class, 'store']);
            Route::delete('/categories/{category}',[CategoryController::class, 'destroy']);
            Route::patch('/categories/{category}',[CategoryController::class, 'update']);
        });
        
    });

    Route::group(['prefix'=>'b1'],function($router){
        Route::get('/brands',[BrandController::class, 'index']);
        Route::get('/brands/{brand}',[BrandController::class, 'productsByBrand']);
        
        Route::group(['middleware'=>'auth'],function($router){
            Route::post('/brands',[BrandController::class, 'store']); 
            Route::delete('/brands/{brand}',[BrandController::class, 'destroy']);
            Route::patch('/brands/{brand}',[BrandController::class, 'update']);
        });
        
    });
    

    
    Route::group(['middleware'=>'auth','prefix'=>'o1'],function($router){
        Route::get('/orders',[OrderController::class, 'index']);
        Route::post('/orders',[OrderController::class, 'store']);
        Route::get('/orders/{user}',[OrderController::class, 'ordersByUser']);
        Route::delete('/orders/{order}',[OrderController::class, 'destroy']);
        Route::patch('/orders/{order}',[OrderController::class, 'update']);
    });
    Route::group(['prefix'=>'a1'],function($router){
        Route::post('/adminCheck',[AdminController::class, 'checkAdmin']);
        
        Route::group(['middleware'=>'auth'],function($router){
            Route::get('/admins',[AdminController::class, 'index']);
            Route::post('/admins',[AdminController::class, 'store']);
            Route::delete('/admins/{admin}',[AdminController::class, 'destroy']);
        });
        
    });
    

    