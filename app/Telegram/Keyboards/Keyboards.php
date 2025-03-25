<?php

namespace App\Telegram\Keyboards;

use Carbon\Carbon;
use Telegram\Bot\Keyboard\Keyboard;

class Keyboards
{
    public static function mainMenuKeyboard()
    {
        return Keyboard::make([
            'keyboard' => [
                [['text' => '📘 Меню'], ['text' => '🛒 Корзина']],
                [['text' => '🔍 Пошук'], ['text' => '📜 Замовлення']],
                [['text' => '❓ FAQ'], ['text' => '📱 Контакти']],
            ],
            'resize_keyboard' => true,
            'one_time_keyboard' => false
        ]);
    }


    public static function categoryKeyboards($data, $rowCount = 2)
    {
        $i = 1;
        $count = count($data) - 1;
        $res = $rowData = [];
        foreach ($data as $k => $category) {
            $rowData[] = [
                'text' => $category->description[0]?->name . " ({$category->products_count})",
                'callback_data' => "query=category&category={$category->category_id}"
            ];
            if ($i === $rowCount or $k === $count) {
                $res[] = $rowData;
                $i = 1;
                $rowData = [];
            } else {
                $i++;
            }
        }
        $res[] = [[
            'text' => "Виробники",
            'callback_data' => "query=manufacturer-list"
        ]];
        return $res;
    }

    public static function cartKeyboard()
    {
        return [
            [
                [
                    'text' => "✏️ Редагувати",
                    'callback_data' => "query=edit-cart&cache-cart-message=1"
                ],
                [
                    'text' => "❌ Видалити",
                    'callback_data' => "query=delete-cart"
                ]
            ],
            [
                [
                    'text' => "✅ Оформити замовлення",
                    'callback_data' => "query=make-order"
                ],
            ]
        ];
    }

    public static function ordersKeyboard($orders)
    {
        $res = [];
        foreach ($orders as $order) {
            $date = Carbon::parse($order->date_added)->format('d/m/Y');
            $res[] = [
                [
                    'text' => "$order->order_id від $date",
                    'callback_data' => "query=order-info&order=$order->order_id"
                ],
            ];
        }
        return $res;
    }
}
