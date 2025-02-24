<?php

namespace App\Telegram\Commands;

use Telegram\Bot\Commands\Command;

class MenuCommand extends Command
{
    protected string $name = 'menu';
    protected string $description = 'меню команда';

    public function handle()
    {
        $text = 'Ось що у нас є:';

        $this->replyWithMessage([
            'text' => 'hardcoded text',
        ]);
    }
}