<?php

namespace App\Telegram\Queries;

use App\Models\Category;
use App\Telegram\Keyboards\Keyboards;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Keyboard\Keyboard;
use Telegram\Bot\Objects\CallbackQuery;

class MenuQuery extends BaseQuery
{
    public static string $name = 'menu';

    public function __construct(CallbackQuery $query, array $params)
    {
        parent::__construct($query, $params);
    }

    public function handle()
    {
        $menu = Category::menuQuery();

        $this->telegram::editMessageText([
            'chat_id' => $this->chatId,
            'message_id' => $this->messageId,
            'text' => "Ось що у нас є:",
            'reply_markup' => Keyboard::make([
                'inline_keyboard' => Keyboards::categoryKeyboards($menu)
            ])
        ]);
    }
}
