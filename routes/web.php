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

Route::get('/', function (){
    $product  = \App\Models\Product::with('special')->where('product_id', 840)->first();
    $options = $product->options()
        ->with('values', function ($q) {
            $q->select(['product_option_id', 'quantity', 'price', 'option_value_id', 'product_option_value_id', 'product_id']);
        })
        ->with('values.special', function ($q) use ($product) {
            $q->where('special_id', $product->special?->product_special_id);
        })
        ->with('values.description')
        ->get(['product_id', 'product_option_id']);
    dd($options[0]->values);
});


Route::get('/product-csv', [\App\Http\Controllers\ExportProductsController::class, 'csv']);

Route::get('/create-order', function () {

    Auth::user()->createOrder();

});

Route::post('/webhook', [\App\Http\Controllers\TelegramController::class, 'shop']);
Route::post('/verify-chat', [\App\Http\Controllers\TelegramController::class, 'verifyChat']);
Route::post('/verify-channel', [\App\Http\Controllers\TelegramController::class, 'verifyChannel']);

