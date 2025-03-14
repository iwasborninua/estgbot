<?php

namespace App\Telegram\Queries\Order;

use App\Telegram\Handlers\Handler;
use App\Telegram\Queries\BaseQuery;
use App\Telegram\TelegramService;
use Telegram\Bot\Keyboard\Keyboard;
use Telegram\Bot\Objects\CallbackQuery;

class RepeatNumberQuery extends BaseQuery
{
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
            'text' => 'Майже закінчили! Ми вже маємо ваш номер телефону. Натисніть на нього, якщо хочете використовувати його, або введіть новий номер',
            'reply_markup' => Keyboard::make([
                'inline_keyboard' => [
                    [
                        [
                            'text' => $delivery['phone'],
                            'callback_data' => "query=total"
                        ]
                    ],
                    [
                        [
                            'text' => "Назад",
                            'callback_data' => "query=select-payment"
                        ]
                    ],
                ]
            ])
        ]);

        TelegramService::setNextAction("total", [
                'prev_message' => $message->messageId,
                'callbackQuery' => $this->query,
                'callbackQueryParams' => $this->params
            ]
        );
    }
}
