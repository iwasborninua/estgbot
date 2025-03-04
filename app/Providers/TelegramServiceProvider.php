<?php

namespace App\Providers;

use App\Telegram\TelegramService;
use App\Telegram\TelegramServiceInterface;
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
        Log::info('Регистрация команд TelegramService...');

        Telegram::addCommands([
            StartCommand::class,
            FaqCommand::class,
            ContactsCommand::class,
            MenuCommand::class
        ]);

        Log::info('Комманды зарегистрированны...');
    }

    public function register()
    {
        $this->app->bind(TelegramServiceInterface::class, function () {


            return new TelegramService(Telegram::getWebhookUpdate());
        });
    }
}
