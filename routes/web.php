<?php

use App\Http\Controllers\BrandController;
use App\Http\Controllers\carController;
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
    return redirect()->route('cars');
})->middleware('auth');

Auth::routes();

Route::middleware('auth')->group( function (){
    Route::get('/cars', function(){
        return view('cars');
    })->name('cars');

    Route::get('/my-leases', function(){
        return view('my-leases');
    })->name('my-leases');

});

Route::middleware(['auth', 'admin'])->group(function (){
    Route::get('/brands', function(){
        return view('brands');
    })->name('brands');

    Route::get('/car-model', function(){
        return view('car-models');
    })->name('car-model');

Route::get('/leases', function(){
        return view('leases');
    })->name('leases');
});