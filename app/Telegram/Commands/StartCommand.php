<?php

namespace App\Telegram\Commands;

use App\Telegram\Keyboards\Keyboards;
use App\Telegram\TelegramService;
use Telegram\Bot\Commands\Command;
use Telegram\Bot\Laravel\Facades\Telegram;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Keyboard\Keyboard;

class StartCommand extends Command
{
    protected string $name = 'start';
    protected string $description = 'Запуск бота';

    public function handle()
    {
        // Первое сообщение (форматировать не стоит...)
        $this->replyWithMessage([
            'text' => "Це офіційний магазин виробника сортового насіння конопель Errors Seeds
bit.ly/ErrorsSeedsShop

Тут ви зможете придбати насіння автоквітучих та фотоперіодних сортів. В наявності 100% фемінізовані індичні та сативні стрейни за найвигіднішими цінами.

Уточнити інформацію щодо оплаченого замовлення можна в t.me/ErrorsSeedsSupportbot

Користуючись меню, переміщайтеся за категоріями, вибирайте товари, складайте їх у кошик і оформляйте замовлення. Бажаємо вдалих покупок!",
            'reply_markup' => Keyboards::mainMenuKeyboard() // Используем клавиатуру из Keyboards.php
        ]);


        // Второе сообщение
        $this->replyWithMessage([
            'text' => "Підписуйтесь на наші соціальні мережі, щоб завжди бути в курсі актуальних новин та пропозицій:",
            'reply_markup' => Keyboard::make([
                'inline_keyboard' => [
                    [
                        ['text' => ' Telegram-канал', 'url' => 'https://t.me/+S1AzQAUTIHg1OTQy'],
                        ['text' => 'Instagram', 'url' => 'https://www.instagram.com/errorsseeds_ua'],
                        ['text' => 'Facebook', 'url' => 'https://www.facebook.com/esuacom'],
                        ['text' => 'ES Grower Club', 'url' => 'https://t.me/+RVZ1Dx51l3xmMzli']
                    ],
                ]
            ])
        ]);
    }
}
// Compare this snippet from app/TelegramService/Commands/StartCommand.php:
