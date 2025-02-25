<?php

namespace App\Telegram\Keyboards;

use App\Models\ProductToCategory;
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


    //todo поменять на цикл
    public static function catalogKeyboards()
    {
        $count = ProductToCategory::query()
            ->selectRaw('count(product_id) as count, category_id')
            ->with('category')
            ->whereHas('category', function ($q) {
                $q->where('status', 1)->where('main_category', 1);
            })->groupBy(['category_id'])->get()->keyBy('category_id')->toArray();

        return [
            [
                ['text' => 'Фемінізовані сорти' . " ({$count[74]['count']})", 'callback_data' => 'category=74'],
                ['text' => 'Автоквітучі фемінізовані сорти' . " ({$count[73]['count']})", 'callback_data' => 'category=73'],
                ['text' => 'Сувенірна продукція' . " ({$count[63]['count']})", 'callback_data' => 'category=63']
            ],
            [
                ['text' => 'Фотоперіодичні сорти' . " ({$count[75]['count']})", 'callback_data' => 'category=75'],
                ['text' => 'Автоквітучі сорти' . " ({$count[76]['count']})", 'callback_data' => 'category=76'],
                ['text' => 'Добрива' . " ({$count[98]['count']})", 'callback_data' => 'category=98']
            ],
            [
                ['text' => 'Друкована продукція' . " ({$count[99]['count']})", 'callback_data' => 'category=99'],
                ['text' => 'Медичний канабіс' . " ({$count[174]['count']})", 'callback_data' => 'category=174'],
                ['text' => 'Їстівна продукція' . " ({$count[196]['count']})", 'callback_data' => 'category=196']
            ],
            [
                ['text' => 'Гідропонні системи' . " ({$count[207]['count']})", 'callback_data' => 'category=207'],
                ['text' => 'Вентиляція' . " ({$count[209]['count']})", 'callback_data' => 'category=209'],
                ['text' => 'Управління та автоматика' . " ({$count[210]['count']})", 'callback_data' => 'category=210']
            ],
            [
                ['text' => 'Вимірювальна техніка' . " ({$count[211]['count']})", 'callback_data' => 'category=211'],
                ['text' => 'Обладнання' . " ({$count[214]['count']})", 'callback_data' => 'category=214'],
            ],
        ];
    }
}
