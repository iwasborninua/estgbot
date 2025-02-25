<?php

namespace App\Telegram\Commands;

use App\Models\Category;
use App\Models\CategoryDescription;
use App\Models\ProductToCategory;
use App\Telegram\Keyboards\Keyboards;
use App\Telegram\TelegramConstants;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Commands\Command;
use Telegram\Bot\Keyboard\Keyboard;

class MenuCommand extends Command
{
    protected string $name = 'menu';
    protected string $description = 'меню команда';

    public function handle()
    {
        $count = ProductToCategory::query()
            ->selectRaw('count(product_id) as count, category_id')
            ->with('category')
            ->whereHas('category', function ($q) {
                $q->where('status', 1)->where('main_category', 1);
            })->groupBy(['category_id'])->get()->keyBy('category_id')->toArray();

        $this->replyWithMessage([
            'text' => "Ось що у нас є:",
            'reply_markup' => Keyboard::make([
                'inline_keyboard' => Keyboards::catalogKeyboards()
            ])
        ]);
    }
}
