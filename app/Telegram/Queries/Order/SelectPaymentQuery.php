<?php

namespace App\Telegram\Queries\Order;

use App\Telegram\Handlers\Handler;
use App\Telegram\Queries\BaseQuery;
use App\Telegram\TelegramService;
use Telegram\Bot\Keyboard\Keyboard;
use Telegram\Bot\Objects\CallbackQuery;

class SelectPaymentQuery extends BaseQuery
{
    public static string $name = 'post';


    public function __construct(CallbackQuery $query, array $params)
    {
        parent::__construct($query, $params);
    }

    public function handle()
    {
        \Telegram::editMessageText([
            "chat_id" => $this->chatId,
            "message_id" => $this->messageId,
            'text' => "Замовлення до 400 грн відправляються за повною передоплатою: " . PHP_EOL . PHP_EOL .
                "Сплатити замовлення можна за реквізитами:" . PHP_EOL . "Карта 4035200041448009 " . PHP_EOL .
                "ФОП Горова Людмила" . PHP_EOL . PHP_EOL . "Після оплати надішліть скріншот чека нашому оператору:",
            'reply_markup' => Keyboard::make(['inline_keyboard' =>
                [
                    [
                        ['text' => "Надіслати чек", 'url' => "http://t.me/ErrorsSeeds_Support_bot"],
                        ['text' => "Хочу консультацію", 'url' => "http://t.me/ErrorsSeeds_Support_bot"],
                    ],
                    [
                        ['text' => "Накладений платіж", 'callback_data' => "query=get-number&payment=" . TelegramService::OVERHEAD_PAYMENT],
                        ['text' => "Я сплатив", 'callback_data' => "query=get-number&payment=" . TelegramService::PAID],
                    ],
                    [['text' => "Назад", 'callback_data' => "query=select-fio"]]
                ]
            ])
        ]);
    }
}
