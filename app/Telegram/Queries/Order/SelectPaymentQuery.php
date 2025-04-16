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
            'text' => "<b><u>Звернiть увагу!!! </u></b>" . PHP_EOL . "<i>• Все обладнання та добрива відправляються на повну оплату новою поштою" . PHP_EOL .
                "• Насіння, аксесуари, суперечки грибів - можна сплатити післяплатою (нова пошта), якщо сума замовлення від 400 до 2500 грн." . PHP_EOL .
                "Умови доставки замовлень з товарами для вирощування можуть відрізнятися. Наші менеджери обов'язково зв'яжуться з вами іповідомлять про
кількість посилок і терміни їх доставки. </i>" . PHP_EOL . PHP_EOL . "<b>Доставка добрив здійснюється тільки після повної предоплати! </b>" . PHP_EOL . PHP_EOL .
                "<b><u>Замовлення до 400 грн відправляються за повною передоплатою:</u></b>" . PHP_EOL . PHP_EOL .
                "<i>Доставка насіння за кордон здійснюється тільки в стелс-упаковці </i>" . PHP_EOL . PHP_EOL .
                "Сплатити замовлення можна за реквізитами:" . PHP_EOL . "Карта 4246001003354598" . PHP_EOL .
                "СИДОРЕНКО МАРІЯ ОЛЕКСАНДРІВНА" . PHP_EOL . PHP_EOL . "Після оплати надішліть скріншот чека нашому оператору:",
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
