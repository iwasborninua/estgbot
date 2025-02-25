<?php

use App\Http\Controllers\QueryController;
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
    $data = ProductToCategory::query()
        ->select(['category_id', 'product_id',])
        ->with(['category' => function ($q) {
            $q->select('category_id');
        }, 'product' => function ($q) {
            $q->select(['product_id', 'quantity', 'price']);
        }, 'product.description' => function ($q) {
            $q->select(['product_id', 'name']);
        }])
        ->whereHas('category', function ($q) {
            $q->where('status', 1)->where('main_category', 1);
        })
        ->whereHas('product.description', function ($q) {
            $q->where('language_id', 3);
        })
        ->whereHas('product', function ($q) {
            $q->where('quantity', '>', 0);
        })
        ->where('category_id', 73)
        ->paginate(10, ['*'], 'page', 5);
    dd($data);
});

Route::get('/test', function () {
});


Route::any('/webhook', function (Request $request) {
    Log::info('Получен запрос на вебхук', ['request' => $request->all()]);

    // Получаем обновление от Telegram
    $update = Telegram::getWebhookUpdates();

    if ($update->objectType() === 'callback_query') {
        $controller = new QueryController($update);
        return $controller->handle();
    }
    // Логирование обновления
    Log::info('Обновление от Telegram: ', ['update' => $update]);

    // Получаем текст сообщения и chat_id
    $messageText = $update->getMessage()->text ?? null;

    if ($messageText) {
        if ($messageText === '❓ FAQ') {
            // Имитируем вызов команды /faq
            Telegram::triggerCommand('faq', $update);
        } elseif ($messageText === '📱 Контакти') {
            Telegram::triggerCommand('contacts', $update);
        } elseif ($messageText === '📘 Меню') {
            Telegram::triggerCommand('menu', $update);
        } else {
            // Если это не "faq", передаем в стандартный обработчик
            $handler = Telegram::commandsHandler(true);
            $query = $handler->get('callback_query');
        }
    }

    return response()->json(['status' => 'ok'], 200);
})->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
