<?php

namespace App\Telegram\Queries\Cart;

use App\Telegram\Queries\BaseQuery;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Keyboard\Keyboard;
use Telegram\Bot\Objects\CallbackQuery;

class AddToCartQuery extends BaseQuery
{
    public static string $name = 'add-to-cart';
    protected $callbackQueryId;

    public function __construct(CallbackQuery $query, array $params)
    {
        parent::__construct($query, $params);

        $this->callbackQueryId = $query->id;
    }

    public function handle()
    {
        $cart = Auth::user()->getCart();
        if (Arr::has($cart, $this->params['product']) and Arr::has($cart[$this->params['product']],  $this->params['pov'])) {
            $cart[$this->params['product']][$this->params['pov']]++;
        } else {
            $cart[$this->params['product']][$this->params['pov']] = 1;
        }

        Auth::user()->setCart($cart);

        $this->telegram::answerCallbackQuery(['text' => "Додано до кошика", 'callback_query_id' => $this->callbackQueryId]);

        $message = $this->query->getMessage();
        $keys = $message->reply_markup->inline_keyboard->toArray();
        foreach ($keys as & $key) {
            if (str_contains($key[0]['callback_data'], $this->params['pov'])) {
                $key[0]['text'] = "(" . $cart[$this->params['product']][$this->params['pov']] . ") " . preg_replace("/^\(\d\)\s/", "", $key[0]['text']);
            }
        }

        $this->telegram::editMessageReplyMarkup([
            'chat_id' => $this->chatId,
            'message_id' => $this->messageId,
            'reply_markup' => Keyboard::make([
                'inline_keyboard' => $keys
            ])
        ]);
    }
}
