<?php

namespace App\Telegram\Queries\Order;

use App\Telegram\Handlers\Handler;
use App\Telegram\Keyboards\Keyboards;
use App\Telegram\Queries\BaseQuery;
use App\Telegram\TelegramService;
use Telegram\Bot\Keyboard\Keyboard;
use Telegram\Bot\Objects\CallbackQuery;

class ConfirmQuery extends BaseQuery
{
    public static string $name = 'confirm';


    public function __construct(CallbackQuery $query, array $params)
    {
        parent::__construct($query, $params);
    }

    public function handle()
    {

        $confirm = $this->params['confirm'];

        $this->telegram::deleteMessage(['chat_id' => $this->chatId,
            'message_id' => $this->messageId,]);
        if ($confirm) {
            $orderId = \Auth::user()->createOrder();
            if ($orderId !== 'ERROR') {
                $this->telegram::sendMessage([
                    'chat_id' => $this->chatId,
                    'text' => "Дякуємо! Ваш номер замовлення: $orderId",
                    'reply_markup' => Keyboards::mainMenuKeyboard()
                ]);
                $this->telegram::sendMessage([
                    'chat_id' => $this->chatId,
                    'text' => "Ви можете продовжити покупки",
                    'reply_markup' => Keyboard::make(['inline_keyboard' =>
                        [[['text' => "Продовжити покупки", 'callback_data' => "query=menu"]]]])
                ]);
            } else {
                $this->telegram::sendMessage([
                    'chat_id' => $this->chatId,
                    'text' => "Сталася помилка. Спробуйте пізніше",
                    'reply_markup' => Keyboards::mainMenuKeyboard()
                ]);
            }

        } else {

            $this->telegram::sendMessage([
                'chat_id' => $this->chatId,
                'text' => "Ви у головному меню, ваш кошик досі актуальний",
                'reply_markup' => Keyboards::mainMenuKeyboard()
            ]);
        }


    }
}
