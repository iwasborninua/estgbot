<?php

use App\Models\CategoryDescription;
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
    return view('welcome');
});

Route::get('/test', function () {
});


Route::any('/webhook', function (Request $request) {
    Log::info('Получен запрос на вебхук', ['request' => $request->all()]);

    // Получаем обновление от Telegram
    $update = Telegram::getWebhookUpdates();

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
            Telegram::commandsHandler(true);
        }
    }

    return response()->json(['status' => 'ok'], 200);
})->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
