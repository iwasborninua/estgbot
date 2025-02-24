<?php

namespace App\Telegram\Commands;

use Telegram\Bot\Commands\Command;

class EmptyCommand extends Command
{
    protected string $name = 'empty';
    protected string $description = 'Пустая команда';

    public function handle()
    {
        $name = 'empty';
        $description = 'Пустая команда';
        
        $text = 'empty';

        $this->replyWithMessage([
            'text' => $text
        ]);
    }
}
// Compare this snippet from app/Telegram/Commands/EmptyCommand.php: