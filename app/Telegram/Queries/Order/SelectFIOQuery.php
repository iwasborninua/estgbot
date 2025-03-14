<?php

namespace App\Telegram\Queries\Order;

use App\Telegram\Handlers\Handler;
use App\Telegram\Queries\BaseQuery;
use App\Telegram\TelegramService;
use Telegram\Bot\Keyboard\Keyboard;
use Telegram\Bot\Objects\CallbackQuery;

class SelectFIOQuery extends BaseQuery
{
    public static string $name = 'post';


    public function __construct(CallbackQuery $query, array $params)
    {
        parent::__construct($query, $params);
    }

    public function handle()
    {
        $delivery = \Auth::user()->getDelivery();

        $message = $this->telegram::editMessageText([
            "chat_id" => $this->chatId,
            "message_id" => $this->messageId,
            'text' => "Напишіть ім'я та прізвище одержувача посилки",
            'reply_markup' => Keyboard::make(['inline_keyboard' =>
                [[['text' => "Назад", 'callback_data' => "query=post&post={$delivery['post']}"]]]])

        ]);

        TelegramService::setNextAction('fio', ['prev_message' => $message->messageId]);
    }
}
