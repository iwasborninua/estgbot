<?php

namespace App\Http\Controllers;

use App\Telegram\TelegramService;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Objects\Update;

class TelegramController extends Controller
{
    private Update $update;
    private string $type;

    public function __construct()
    {
        $this->update = Telegram::getWebhookUpdate();
        $this->type = str_replace("_", "", ucwords($this->update->objectType(), " _"));
    }

    //dispatcher
    public function __invoke(TelegramService $telegramService)
    {
        try {
            $method = $this->type . "Handler";
            if (method_exists($telegramService, $method)) {
                return $telegramService->{$method}($this->update);
            } else {
                Log::error("Unknown object Type[{$this->type}]. No Handle method");
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }

    }
}
