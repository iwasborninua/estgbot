<?php

namespace App\Telegram\Commands;

use App\Telegram\TelegramConstants;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Commands\Command;

class MenuCommand extends Command
{
    protected string $name = 'menu';
    protected string $description = 'меню команда';

    public function handle()
    {
        $message = request()['message'];


        if ($message['text'] == TelegramConstants::MENU) {
            $this->replyWithMessage([
                'text' => 'TelegramConstants',
            ]);
        } else {
            $this->replyWithMessage([
                'text' => 'hardcoded text',
            ]);
        }

    }
}
