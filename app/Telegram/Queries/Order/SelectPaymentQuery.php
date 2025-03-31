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
        $paymentTypes = [];

        if (\Auth::user()->getCartTotal() >= config('delivery.overhead_payment_min_amount')) {
            $paymentTypes[] = ['text' => "Накладений платіж", 'callback_data' => "query=get-number&payment=" . TelegramService::OVERHEAD_PAYMENT];
        }

        $paymentTypes[] = ['text' => "Я сплатив", 'callback_data' => "query=get-number&payment=" . TelegramService::PAID];

        \Telegram::editMessageText([
            "chat_id" => $this->chatId,
            "message_id" => $this->messageId,
            'text' => "<span style='color:#f34848'>Звернiть увагу!!! " . PHP_EOL . PHP_EOL . "
            • Все обладнання та добрива відправляються на повну оплату новою поштою" . PHP_EOL . "
              • Насіння, аксесуари, суперечки грибів - можна сплатити післяплатою (нова пошта), якщо сума замовлення від 400 до 2500 грн." . PHP_EOL . "
            Умови доставки замовлень з товарами для вирощування можуть відрізнятися. Наші менеджери обов'язково зв'яжуться з вами і
            повідомлять про кількість посилок і терміни їх доставки. </span>" . PHP_EOL . PHP_EOL .
                "<span style='color: red'>Доставка добрив здійснюється тільки після повної предоплати! </span>" . PHP_EOL . PHP_EOL .
                "<span style='color: #b52323'>Замовлення до 400 грн відправляються за повною передоплатою: </span>" . PHP_EOL . PHP_EOL .
                "<span style='color: #952626'>Доставка насіння за кордон здійснюється тільки в стелс-упаковці </span>" . PHP_EOL . PHP_EOL .
                "Сплатити замовлення можна за реквізитами:" . PHP_EOL . "Карта 4035200041448009 " . PHP_EOL .
                "ФОП Горова Людмила" . PHP_EOL . PHP_EOL . "Після оплати надішліть скріншот чека нашому оператору:",
            'reply_markup' => Keyboard::make(['inline_keyboard' =>
                [
                    [
                        ['text' => "Надіслати чек", 'url' => "http://t.me/ErrorsSeeds_Support_bot"],
                        ['text' => "Хочу консультацію", 'url' => "http://t.me/ErrorsSeeds_Support_bot"],
                    ],
                    $paymentTypes,
                    [['text' => "Назад", 'callback_data' => "query=select-fio"]]
                ]
            ])
        ]);
    }
}
