<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiAuthController;
use App\Http\Controllers\ContactApiController;
use App\Http\Controllers\FavouriteApiController;

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

Route::post('/register',[ApiAuthController::class,'register'])->name('api.register');
Route::post("/login",[ApiAuthController::class,'login'])->name('api.login');

Route::middleware('auth:sanctum')->group(function () {
    Route::post("/logout",[ApiAuthController::class,'logout'])->name('api.logout');
    Route::post("/logout-all",[ApiAuthController::class,'logoutAll'])->name('api.logout-all');
    Route::apiResource("contacts",ContactApiController::class);
    Route::apiResource("favourites",FavouriteApiController::class);
});

