<?php

namespace App\Telegram\Commands;

use App\Models\Category;
use App\Models\CategoryDescription;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductDescription;
use App\Models\ProductOptionValue;
use App\Models\ProductToCategory;
use App\Telegram\Keyboards\Keyboards;
use App\Telegram\TelegramService;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Commands\Command;
use Telegram\Bot\Keyboard\Keyboard;

class FallBackCommand extends Command
{
    protected string $name = 'fallback';
    protected string $description = 'Error Handler';

    public function handle()
    {
        $this->replyWithMessage([
            'text' => "Сталася помилка. Розробники все працюють над нею. Спробуйте трошки пізнше",
            'reply_markup' => Keyboards::mainMenuKeyboard()
        ]);
    }
}
