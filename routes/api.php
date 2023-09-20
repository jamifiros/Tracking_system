<?php

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });


    Route::post('login',[ApiController::class,'login'])->name('login');
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout',[ApiController::class,'logout'])->name('logout');
        Route::get('/getlist/{id}',[ApiController::class,'getlist'])->name('getlist');
        Route::post('/addlist/{id}',[ApiController::class,'addlist'])->name('addlist');
        Route::post('/store',[ApiController::class,'store'])->name('store');
        Route::get('/show/{id}',[ApiController::class,'show'])->name('show');
        Route::put('/update/remarks/{id}',[ApiController::class,'update'])->name('update');
    });