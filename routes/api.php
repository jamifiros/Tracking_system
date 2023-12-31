<?php

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ForgotPasswordController;

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

Route::post('login',[ApiController::class,'login'])->name('login');
    Route::middleware('auth:sanctum')->group(function () 
    {
        Route::post('logout',[ApiController::class,'logout'])->name('logout');
        Route::get('/getlist/{id}',[ApiController::class,'getlist'])->name('getlist');
        Route::post('/addlist/{id}',[ApiController::class,'addlist'])->name('addlist');
        Route::post('/store',[ApiController::class,'store'])->name('store');
        Route::get('/show/visit/{id}',[ApiController::class,'show'])->name('show');
        Route::put('/update/{id}',[ApiController::class,'update'])->name('update');
        Route::get('/pending/{id}',[ApiController::class,'pendinglist'])->name('pendinglist');
        Route::put('/update/password/{id}',[ApiController::class,'password'])->name('password');
        Route::post('/update/profileimage/{id}',[ApiController::class,'changeprofile'])->name('changeprofile');
        Route::get('/show/updatedimage/{id}',[ApiController::class,'showprofile'])->name('showprofile');

    });
  
    Route::post('/forgot/password',[ForgotPasswordController::class,'forgot'])->name('forgot');
    Route::post('/checkcode',[ForgotPasswordController::class,'Check'])->name('Check');