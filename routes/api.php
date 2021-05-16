<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CityController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ImageController;
use App\Http\Controllers\Api\StateController;
use App\Http\Controllers\Api\CountryController;
use App\Http\Controllers\Api\ShippingController;

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


Route::middleware('auth:api')->get('/users/auth/notifications', function (Request $request) {
    return response()->json(['notifications' => $request->user()->notifications]);
});

Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login'])->name('users.login');
    Route::post('/register', [AuthController::class, 'register'])->name('users.register');
    Route::post('/forget', [AuthController::class, 'forget'])->name('users.forget');
    Route::post('/reset', [AuthController::class, 'reset'])->name('users.reset');
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:api')->name('users.logout');
    Route::middleware('auth:api')->group(function () {
        Route::get('/me', [AuthController::class, 'me'])->name('users.me');
        Route::post('/me', [AuthController::class, 'updateProfile'])->name('users.updateProfile');
        Route::get('/me/valid', [AuthController::class, 'checkValidation'])->name('users.me');
    });
});


Route::prefix('shipping')->group(function() {
    Route::post('rate', [ShippingController::class, 'calculateRate']);
    Route::post('shipment/{shippingProvider}', [ShippingController::class, 'createShipment']);
});
Route::resource('/countries', CountryController::class);
Route::resource('/cities', CityController::class);
Route::resource('/states', StateController::class);
Route::post('/image/upload', [ImageController::class, 'upload']);
