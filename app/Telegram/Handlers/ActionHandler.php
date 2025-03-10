<?php

namespace App\Telegram\Handlers;

use App\Telegram\Queries\Cart\EditCartQuery;
use App\Telegram\TelegramService;
use Telegram\Bot\Objects\Update;

class ActionHandler implements Handler
{
    private $params;

    public function __construct(private readonly Update $update)
    {
        $this->params = cache(\Auth::id() . TelegramService::NEXT_ACTION_PARAMS);
    }

    public function insert()
    {
        $message = $this->update->message;

        if (is_numeric($message->text)) {
            $cart = \Auth::user()->getCart();
            $cart[$this->params['callbackQueryParams']['product']][$this->params['callbackQueryParams']['pov']] = $message->text;
            \Auth::user()->setCart($cart);

            $query = new EditCartQuery($this->params['callbackQuery'], $this->params['callbackQueryParams']);
            $query->handle();

            \Telegram::editMessageText([
                'chat_id' => $message->chat->id,
                'message_id' => $this->params['insert_message_id'],
                'text' => 'Добре :)'
            ]);
            sleep(2);
            \Telegram::deleteMessage(['chat_id' => $message->chat->id, 'message_id' => $message->messageId]);
            \Telegram::deleteMessage(['chat_id' => $message->chat->id, 'message_id' => $this->params['insert_message_id']]);

        } else {
            \Telegram::deleteMessage(['chat_id' => $message->chat->id, 'message_id' => $message->messageId]);
            try {
                \Telegram::editMessageText([
                    'chat_id' => $message->chat->id,
                    'message_id' => $this->params['insert_message_id'],
                    'text' => 'Кiлькiсть товару повинна бути числом.'
                ]);
                TelegramService::setNextAction('insert', $this->params);

            } catch (\Exception $e) {
                TelegramService::setNextAction('insert', $this->params);

            }
        }
    }
}
