<?php

namespace App\Http\Controllers;

use App\Telegram\TelegramServiceInterface;

class TelegramController extends Controller
{
    //dispatcher
    public function __invoke(TelegramServiceInterface $telegramService)
    {
        $telegramService->authUser();
        $telegramService->handleUpdate();
    }
}
