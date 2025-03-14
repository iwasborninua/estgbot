<?php

namespace App\Telegram\Queries\Order;

use App\Telegram\Handlers\Handler;
use App\Telegram\Queries\BaseQuery;
use App\Telegram\TelegramService;
use Telegram\Bot\Keyboard\Keyboard;
use Telegram\Bot\Objects\CallbackQuery;

class TotalQuery extends BaseQuery
{
    public static string $name = 'post';


    public function __construct(CallbackQuery $query, array $params)
    {
        parent::__construct($query, $params);
    }

    public function handle()
    {
        $delivery = \Auth::user()->getDelivery();
        \Auth::user()->setDelivery($delivery);

        $text = TelegramService::cartText();
        $post = config('delivery.post.' . $delivery['post']);
        $postType = config('delivery.post-type.' . $delivery['post']);
        $paymentType = config('delivery.payment-type.' . $delivery['post']);
        $paymentTitle = config('delivery.post-type.' . $delivery['payment']);

        $text .= PHP_EOL . PHP_EOL;
        $text .= "<b>Адреса доставки:</b> {$delivery['city']}" . PHP_EOL;
        $text .= "<b>Метод доставки:</b> $post" . PHP_EOL;
        $text .= "<b>$postType:</b> {$delivery['postData']}" . PHP_EOL;
        $text .= "<b>ФИО Отримувача:</b> {$delivery['fio']}" . PHP_EOL;
        $text .= "<b>$paymentType:</b> $paymentTitle" . PHP_EOL;
        $text .= "<b>Запит номера:</b> {$delivery['phone']}" . PHP_EOL;

        $message = \Telegram::editMessageText([
            'chat_id' => $this->chatId,
            'message_id' => $this->messageId,
            'text' => $text,
            'parse_mode' => 'html',
            'reply_markup' => Keyboard::make([
                'inline_keyboard' => [
                    [
                        ['text' => "Так", 'callback_data' => "query=confirm-order&confirm=" . 1],
                        ['text' => "Ні, отмєна", 'callback_data' => "query=confirm-order&confirm=" . 0],
                    ],
                    [['text' => "Назад", 'callback_data' => "query=repeat-number"]
                    ]
                ],
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
