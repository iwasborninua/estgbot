<?php

namespace App\Telegram\Queries\Order;

use App\Telegram\Handlers\Handler;
use App\Telegram\Queries\BaseQuery;
use App\Telegram\TelegramService;
use Telegram\Bot\Keyboard\Keyboard;
use Telegram\Bot\Objects\CallbackQuery;

class SelectPostQuery extends BaseQuery
{
    public static string $name = 'post';


    public function __construct(CallbackQuery $query, array $params)
    {
        parent::__construct($query, $params);
    }

    public function handle()
    {
        $this->telegram::editMessageText([
            "chat_id" => $this->chatId,
            "message_id" => $this->messageId,
            'text' => 'Чим доставляти?',
            'reply_markup' => Keyboard::make([
                'inline_keyboard' => [
                    [
                        [
                            'text' => "Нова Пошта",
                            'callback_data' => "query=post&post=" . TelegramService::NOVAPOST
                        ],
                        [
                            'text' => "Укрпошта",
                            'callback_data' => "query=post&post=" . TelegramService::UKRPOST
                        ]
                    ],
//                    [
//                        [
//                            'text' => "Укрпошта (Казахстан)",
//                            'callback_data' => "query=post&post=" . TelegramService::UKRPOST_KZ
//                        ],
//                    ],
                    [
                        [
                            'text' => "Назад",
                            'callback_data' => "query=make-order"
                        ],
                    ]
                ]
            ])
        ]);
    }
}
