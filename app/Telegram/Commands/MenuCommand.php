<?php

namespace App\Telegram\Commands;

use Illuminate\Support\Facades\Log;
use Telegram\Bot\Commands\Command;

class MenuCommand extends Command
{
    protected string $name = 'menu';
    protected string $description = 'меню команда';

    public function handle()
    {
        Log::info(request());

        $this->replyWithMessage([
            'text' => 'hardcoded text',
        ]);
    }
}
