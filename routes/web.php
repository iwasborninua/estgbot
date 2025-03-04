<?php

use App\Models\Product;
use App\Models\ProductOptionValue;
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

    $product = Product::find(285);
    $options = $product->options()
        ->with('values', function ($q) {
            $q->select(['product_option_id', 'quantity', 'price', 'option_value_id', 'product_option_value_id']);
        })
        ->with('values.description')
        ->get(['product_id', 'product_option_id']);
    dd($options);
});

Route::get('/test', function () {
});

Route::post('/webhook', \App\Http\Controllers\TelegramController::class);

