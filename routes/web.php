<?php

use App\Models\Category;
use App\Models\Setting;
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

Route::get('/product-csv', [\App\Http\Controllers\ExportProductsController::class, 'csv']);

Route::get('/create-order', function () {

    Auth::user()->createOrder();

});

Route::post('/webhook', [\App\Http\Controllers\TelegramController::class, 'shop']);
Route::post('/verify-chat', [\App\Http\Controllers\TelegramController::class, 'verifyChat']);
Route::post('/verify-channel', [\App\Http\Controllers\TelegramController::class, 'verifyChannel']);

