<?php

namespace App\Providers;

use App\Telegram\Commands\CartCommand;
use App\Telegram\Commands\FallBackCommand;
use App\Telegram\Commands\OrderCommand;
use App\Telegram\Handlers\ActionHandler;
use App\Telegram\Handlers\Handler;
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
        Telegram::addCommands([
            StartCommand::class,
            FaqCommand::class,
            ContactsCommand::class,
            MenuCommand::class,
            CartCommand::class,
            OrderCommand::class,
            FallBackCommand::class
        ]);
    }

    public function register()
    {
        $this->app->bind(TelegramServiceInterface::class, function () {
            return new TelegramService(Telegram::getWebhookUpdate());
        });
        $this->app->bind(Handler::class, function () {
            return new ActionHandler(Telegram::getWebhookUpdate());
        });
    }
}
