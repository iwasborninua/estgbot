<?php

namespace App\Telegram\Queries\Order;

use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Product;
use App\Models\ProductDescription;
use App\Models\ProductOptionValue;
use App\Telegram\Handlers\Handler;
use App\Telegram\Keyboards\Keyboards;
use App\Telegram\Queries\BaseQuery;
use App\Telegram\TelegramService;
use Telegram\Bot\Keyboard\Keyboard;
use Telegram\Bot\Objects\CallbackQuery;

class InfoQuery extends BaseQuery
{
    public static string $name = 'order-info';


    public function __construct(CallbackQuery $query, array $params)
    {
        parent::__construct($query, $params);
    }

    public function handle()
    {
        $orderId = $this->params['order'];
        $orderData = Order::query()->where('order_id', $orderId)->first();
        $orderProduct = OrderProduct::query()->with('orderOption')->where('order_id', $orderId)->get();
        $postType = config('delivery.post-type.' . $orderData->shipping_code, 'Відділення');
        $paymentType = config('delivery.payment-type.' . $orderData->shipping_code, 'Оплата');
        $paymentTitle = ($orderData->payment_code == 'pb_card' ? "Я Сплатив" : "Накладений платіж");

        $text = "<b>Кошик</b>" . PHP_EOL . PHP_EOL . "-----" . PHP_EOL;
        foreach ($orderProduct as $product) {
            $name = ProductDescription::query()
                ->where('product_id', $product->product_id)
                ->where('language_id', config('constants.lang'))
                ->value('name');

            $text .= "<b>$name</b>" . PHP_EOL;

            $text .= $product->orderOption->value . ": " . $product->quantity . "шт. x " . $product->price . "₴ = " . $product->total . "₴" .
                PHP_EOL;
            $text .= PHP_EOL;
        }
        $text .= "-----" . PHP_EOL . "<b>Загалом:</b> {$orderData->total}₴" . PHP_EOL . PHP_EOL;

        $text .= "<b>Адреса доставки:</b> {$orderData->payment_city}, {$orderData->payment_zone}" . PHP_EOL;
        $text .= "<b>Метод доставки:</b> {$orderData->shipping_method}" . PHP_EOL;
        $text .= "<b>$postType:</b> {$orderData->shipping_address_1}" . PHP_EOL;
        $text .= "<b>ФИО Отримувача:</b> {$orderData->lastname} {$orderData->firstname}" . PHP_EOL;
        $text .= "<b>$paymentType:</b> $paymentTitle" . PHP_EOL;
        $text .= "<b>Запит номера:</b> {$orderData->telephone}" . PHP_EOL;

        $this->telegram::sendMessage([
            'chat_id' => $this->chatId,
            'text' => $text,
            'parse_mode' => 'html'
        ]);
    }
}
