<?php

use App\Http\Controllers\BrandController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home')->middleware('auth');


Route::middleware(['auth', 'admin'])->group(function (){
    Route::get('/brands', function(){
        return view('brands');
    })->name('brands');

    Route::get('/cars', function(){
        return view('cars');
    })->name('cars');

    Route::get('/car-model', function(){
        return view('car-models');
    })->name('car-model');
});