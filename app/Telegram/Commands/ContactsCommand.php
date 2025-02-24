<?php

namespace App\Telegram\Commands;

use Telegram\Bot\Commands\Command;

class ContactsCommand extends Command
{
    protected string $name = 'contacts';
    protected string $description = 'Контакты';

    public function handle()
    {
        $text = 'Телефони:
849 (За тарифами оператора)
  +38 096 00 00 849
  +38 095 00 00 849
  +38 093 00 00 849
Безкоштовно з будь-якого оператора:
  +380 800 750 849
  +380 800 750 938

Ви завжди можете нам написати!
Наш e-mail: autoryder@gmail.com

Приєднуйтесь до нас у телеграм https://t.me/+4GxvYp5PIgkxZThi
Зв\'язок у телеграм - @ErrorsSeedsSupportbot';

        $this->replyWithMessage([
            'text' => $text
        ]);
    }
}
// Compare this snippet from app/Telegram/Commands/ContactsCommand.php: