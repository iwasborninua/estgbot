<?php

namespace App\Telegram\Keyboards;

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
}