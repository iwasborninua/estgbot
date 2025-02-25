<?php

use App\Http\Controllers\QueryController;
use App\Models\Category;
use App\Models\CategoryDescription;
use App\Models\Product;
use App\Models\ProductToCategory;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Laravel\Facades\Telegram;


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

});

Route::get('/test', function () {
});

Route::post('/webhook', \App\Http\Controllers\TelegramController::class);

