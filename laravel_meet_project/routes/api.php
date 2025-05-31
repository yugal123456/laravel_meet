<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\RoomController;

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

// Public routes
Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);


// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('user', function (Request $request) {
        return $request->user();
    });
    Route::get('/my-bookings', [App\Http\Controllers\BookingController::class, 'index']);
    Route::resource('bookings', App\Http\Controllers\BookingController::class);
    Route::get('/rooms', [App\Http\Controllers\RoomController::class, 'index']);
    Route::post('/subscribe', [App\Http\Controllers\SubscriptionController::class, 'subscribe']);

    // Room routes
    Route::apiResource('rooms', RoomController::class);
});
