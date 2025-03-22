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

class OrderCommand extends Command
{
    protected string $name = 'order';
    protected string $description = 'Замовлення';

    public function handle()
    {
        $orders = Order::query()->where('customer_id', \Auth::id())->get(['order_id', 'date_added']);

        return $this->replyWithMessage([
            'text' => "Ваші замовлення:",
            'parse_mode' => 'html',
            'reply_markup' => Keyboard::make([
                'inline_keyboard' => Keyboards::ordersKeyboard($orders)
            ])
        ]);
    }
}
