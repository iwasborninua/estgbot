<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Telegram\Bot\Laravel\Facades\Telegram;
use App\Telegram\Commands\StartCommand;
use App\Telegram\Commands\FaqCommand;
use App\Telegram\Commands\ContactsCommand;
use App\Telegram\Commands\MenuCommand;
use Illuminate\Support\Facades\Log;

class TelegramServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Log::info('Регистрация команд Telegram...');

        Telegram::addCommands([
            StartCommand::class,
            FaqCommand::class,
            ContactsCommand::class,
            MenuCommand::class
        ]);

        Log::info('Комманды зарегистрированны...');
    }
}
