<?php

namespace App\Telegram\Commands;

use App\Models\Category;
use App\Models\CategoryDescription;
use App\Models\ProductToCategory;
use App\Telegram\Keyboards\Keyboards;
use App\Telegram\TelegramService;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Commands\Command;
use Telegram\Bot\Keyboard\Keyboard;

class MenuCommand extends Command
{
    protected string $name = 'menu';
    protected string $description = 'меню команда';

    public function handle()
    {
        $menu = Category::menuQuery();

        $this->replyWithMessage([
            'text' => "Ось що у нас є:",
            'reply_markup' => Keyboard::make([
                'inline_keyboard' => Keyboards::categoryKeyboards($menu)
            ])
        ]);
    }
}
