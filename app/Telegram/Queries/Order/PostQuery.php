<?php

namespace App\Telegram\Queries\Order;

use App\Telegram\Queries\BaseQuery;
use App\Telegram\TelegramService;
use Telegram\Bot\Keyboard\Keyboard;
use Telegram\Bot\Objects\CallbackQuery;

class PostQuery extends BaseQuery
{
    public static string $name = 'post';


    public function __construct(CallbackQuery $query, array $params)
    {
        parent::__construct($query, $params);
    }

    public function handle()
    {
        $post = $this->params['post'];
        $delivery = \Auth::user()->getDelivery();
        $delivery['post'] = $post;
        \Auth::user()->setDelivery($delivery);
        if ($post === 'novapost') {
            $text = "Напишіть № відділення Нової Пошти, де буде зручно забрати посилку. (Попередньо переконайтесь у роботі відділення)";
        } else {
            $text = "Напишіть індекс куди надсилати замовлення. (Попередньо переконайтесь у роботі відділення)";
        }

        $message = $this->telegram::editMessageText([
            'chat_id' => $this->chatId,
            'message_id' => $this->messageId,
            'text' => $text,
            'reply_markup' => Keyboard::make(['inline_keyboard' => [[['text' => "Назад", 'callback_data' => "query=select-post"],]]])
        ]);

        TelegramService::setNextAction("postdata", [
                'prev_message' => $message->messageId,
                'callbackQuery' => $this->query,
                'callbackQueryParams' => $this->params
            ]
        );
    }
}
