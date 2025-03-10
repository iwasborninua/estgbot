<?php

namespace App\Telegram\Commands;

use App\Models\Category;
use App\Models\CategoryDescription;
use App\Models\Product;
use App\Models\ProductDescription;
use App\Models\ProductOptionValue;
use App\Models\ProductToCategory;
use App\Telegram\Keyboards\Keyboards;
use App\Telegram\TelegramService;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Commands\Command;
use Telegram\Bot\Keyboard\Keyboard;

class CartCommand extends Command
{
    protected string $name = 'cart';
    protected string $description = 'Корзина';

    public function handle()
    {
        $text = TelegramService::cartText();

        if (empty($text)) {
            return $this->replyWithMessage([
                'text' => 'У кошику нічого немає'
            ]);
        }

        return $this->replyWithMessage([
            'text' => $text,
            'parse_mode' => 'html',
            'reply_markup' => Keyboard::make([
                'inline_keyboard' => Keyboards::cartKeyboard()
            ])
        ]);
    }
}
