<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsersController;
// use App\Http\Controllers\ContactController;
use App\Http\Controllers\OrderController;
// use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\GlossaryController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\DashboardController;
// use App\Http\Controllers\DocumentGeneratorController;
// use App\Http\Controllers\StatisticController;
// use App\Http\Controllers\SubscriberController;
// use App\Http\Controllers\DocumentTypeController;
use App\Http\Controllers\ShipmentController;
use App\Http\Controllers\StateController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', DashboardController::class)->middleware(['auth','verified']);

Route::resource('/users', UsersController::class)->middleware(['auth','verified']);
Route::resource('/countries', CountryController::class)->middleware(['auth','verified']);
Route::resource('/cities', CityController::class)->middleware(['auth','verified']);
Route::resource('/states', StateController::class)->middleware(['auth','verified']);
Route::get('/change_password', [UsersController::class,'change_password_form'])->name('users.change_password_form')->middleware(['auth','verified']);
Route::post('/change_password', [UsersController::class,'change_password'])->name('users.change_password')->middleware(['auth','verified']);
Route::get('/profile', [UsersController::class,'profile_form'])->name('users.profile_form')->middleware(['auth','verified']);
Route::post('/profile', [UsersController::class,'update_profile'])->name('users.profile')->middleware(['auth','verified']);

// Route::resource('/categories', CategoryController::class)->middleware(['auth','verified']);
Route::resource('/settings', SettingsController::class)->middleware(['auth','verified'])->only(['index','store']);
// Route::resource('/contacts', ContactController::class)->middleware(['auth','verified'])->only(['index','destroy']);
// Route::resource('/document-generator', DocumentGeneratorController::class)->middleware(['auth','verified'])->only(['index','destroy']);
// Route::post('/document-generator/send_url', [DocumentGeneratorController::class,'send_url'])->name('document-generator.send_url')->middleware(['auth','verified']);

// Route::resource('/document_types',DocumentTypeController::class);

// Route::resource('/subscribers', SubscriberController::class)->middleware(['auth','verified']);
// Route::resource('/glossaries', GlossaryController::class)->middleware(['auth','verified']);
Route::resource('/orders', OrderController::class)->middleware(['auth','verified']);
Route::resource('/shipments', ShipmentController::class)->middleware(['auth','verified']);

// Route::get('/views', [StatisticController::class,'index'])->name('views.index')->middleware(['auth','verified']);
// Route::post('/views/reset/{view}', [StatisticController::class,'reset'])->name('views.reset')->middleware(['auth','verified']);

Auth::routes(['verify'=>true]);

// Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
