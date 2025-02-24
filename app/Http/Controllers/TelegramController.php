<?php

namespace App\Http\Controllers;

use Telegram\Bot\Laravel\Facades\Telegram;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class TelegramController extends Controller
{
    public function handle(Request $request)
    {
    }

    public function sendMessage()
    {
        $response = Telegram::sendMessage([
            'chat_id' => 'YOUR_CHAT_ID',
            'text' => 'Hello World'
        ]);
        return $response;
    }
}
