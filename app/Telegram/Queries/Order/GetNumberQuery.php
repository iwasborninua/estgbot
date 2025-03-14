<?php

namespace App\Telegram\Queries\Order;

use App\Telegram\Handlers\Handler;
use App\Telegram\Queries\BaseQuery;
use App\Telegram\TelegramService;
use Telegram\Bot\Keyboard\Keyboard;
use Telegram\Bot\Objects\CallbackQuery;

class GetNumberQuery extends BaseQuery
{
    public static string $name = 'post';


    public function __construct(CallbackQuery $query, array $params)
    {
        parent::__construct($query, $params);
    }

    public function handle()
    {
        $payment = $this->params['payment'];
        $delivery = \Auth::user()->getDelivery();
        $delivery['payment'] = $payment;
        \Auth::user()->setDelivery($delivery);

        $this->telegram::deleteMessage(['chat_id' => $this->chatId,
            'message_id' => $this->messageId,]);

        $message = $this->telegram::sendMessage([
            'chat_id' => $this->chatId,
            'text' => "Майже готово! Поділіться з нами вашим номером телефону або напишіть його нижче",
            'reply_markup' => Keyboard::make([
                'keyboard' => [
                    [Keyboard::button(['text' => 'Поділитися номером', 'request_contact' => true,])],
                    [Keyboard::button(['text' => TelegramService::CANCEL])],
                ],
                'resize_keyboard' => true,
                'one_time_keyboard' => true
            ]),
        ]);

        TelegramService::setNextAction("total", [
                'prev_message' => $message->messageId,
                'callbackQuery' => $this->query,
                'callbackQueryParams' => $this->params
            ]
        );
    }
}
