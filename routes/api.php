<?php

use App\Http\Controllers\BrandController;
use App\Http\Controllers\CarController;
use App\Http\Controllers\CarModelController;
use App\Http\Controllers\CostumerController;
use App\Http\Controllers\LeaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::middleware('jwt.auth')->group(function(){
    Route::apiResource('costumer', CostumerController::class);

    Route::apiResource('brand', BrandController::class);
    
    Route::apiResource('car', CarController::class);
    
    Route::apiResource('lease', LeaseController::class);
    
    Route::apiResource('car-model', CarModelController::class);
});


Route::post('login', [AuthController::class, 'login']);
Route::post('logout',  [AuthController::class, 'logout']);
Route::post('refresh',  [AuthController::class, 'refresh']);
Route::post('me',  [AuthController::class, 'me']);