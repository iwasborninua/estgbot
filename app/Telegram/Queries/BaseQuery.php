<?php

namespace App\Telegram\Queries;

use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Objects\CallbackQuery;

abstract class BaseQuery
{
    protected $telegram;
    protected $chatId;
    protected $messageId;

    public function __construct(protected CallbackQuery $query, protected array $params)
    {
        $this->telegram = Telegram::class;
        $this->chatId = $this->query->message->chat->id;
        $this->messageId = $this->query->message->messageId;
    }


    abstract public function handle();
}
