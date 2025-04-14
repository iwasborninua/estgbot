<?php

namespace App\Http\Controllers;

use App\Telegram\TelegramServiceInterface;

class TelegramController extends Controller
{
    //dispatcher
    public function shop(TelegramServiceInterface $telegramService)
    {
        $telegramService->authUser();
        $telegramService->handleUpdate();
    }

    public function verifyChat()
    {

        $update = \Telegram::getWebhookUpdate();
        $adminUsername = "ErrorsSeed_admin";

        $welcomeImagePath = storage_path('app/public/verified.jpg');
        if (isset($update['chat_join_request'])) {
            $chatJoinRequest = $update['chat_join_request'];
            $userId = $chatJoinRequest['from']['id'];
            $caption = "***Вітаємо! Для підтвердження запиту у канал, будь ласка, напишіть 'хочу в чат' адміністратору: [@" . $adminUsername . "]***\n\n";
            $caption .= "Ми перевіряємо кожного вручну — це вимушений крок через атаки з боку ворожих ботів. Так ми захищаємо спільноту від фейкових акаунтів, витоку інформації та блокувань. Дякуємо за розуміння! 💛";

            try {
                // Отправляем фото с подписью
                \Telegram::sendPhoto([
                    'chat_id' => $userId,
                    'photo' => new \CURLFile(realpath($welcomeImagePath)),
                    'caption' => $caption,
                    'parse_mode' => 'Markdown'
                ]);
            } catch (\Exception $e) {
                \Log::info($e, ['exception']);
                // Если отправка фото не удалась, отправляем только текст
                \Telegram::sendMessage([
                    'chat_id' => $userId,
                    'text' => $caption,
                    'parse_mode' => 'Markdown'
                ]);
            }
        }

        return response('');
    }

    public function verifyChannel()
    {
        $adminUsername = "ErrorsSeed_admin";

// Путь к изображению
        $welcomeImagePath = storage_path('app/public/verified.jpg');
        $update = \Telegram::getWebhookUpdate();

        if (isset($update['chat_join_request'])) {
            $chatJoinRequest = $update['chat_join_request'];
            $userId = $chatJoinRequest['from']['id'];
            $caption = "***Вітаємо! Для підтвердження запиту у канал, будь ласка, напишіть '+' адміністратору: [@" . $adminUsername . "]***\n\n";
            $caption .= "Ми перевіряємо кожного вручну — це вимушений крок через атаки з боку ворожих ботів. Так ми захищаємо спільноту від фейкових акаунтів, витоку інформації та блокувань. Дякуємо за розуміння! 💛";

            try {
                // Отправляем фото с подписью
                \Telegram::sendPhoto([
                    'chat_id' => $userId,
                    'photo' => new \CURLFile(realpath($welcomeImagePath)),
                    'caption' => $caption,
                    'parse_mode' => 'Markdown'
                ]);
            } catch (\Exception $e) {
                \Log::info($e, ['exception']);
                // Если отправка фото не удалась, отправляем только текст
                \Telegram::sendMessage([
                    'chat_id' => $userId,
                    'text' => $caption,
                    'parse_mode' => 'Markdown'
                ]);
            }
        }
    }
}
