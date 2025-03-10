<?php

namespace App\Telegram\Queries\Cart;

use App\Models\Product;
use App\Models\ProductDescription;
use App\Models\ProductOptionValue;
use App\Telegram\Keyboards\Keyboards;
use App\Telegram\Queries\BaseQuery;
use App\Telegram\TelegramService;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Telegram\Bot\FileUpload\InputFile;
use Telegram\Bot\Keyboard\Keyboard;
use Telegram\Bot\Objects\CallbackQuery;
use Telegram\Bot\Objects\InputMedia\InputMedia;

class UpdateCartQuery extends BaseQuery
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
        $this->{$this->params['method']}();
    }

    private function plus()
    {
        $cart = Auth::user()->getCart();
        $cart[$this->params['product']][$this->params['pov']]++;
        Auth::user()->setCart($cart);

        $this->params['next-edit'] = 1;
        (new EditCartQuery($this->query, $this->params))->handle();
    }

    private function minus()
    {
        $cart = Auth::user()->getCart();
        $cart[$this->params['product']][$this->params['pov']]--;
        Auth::user()->setCart($cart);

        $this->params['next-edit'] = 1;
        (new EditCartQuery($this->query, $this->params))->handle();
    }

    public function insert()
    {
        $this->params['next-edit'] = 1;
        $message = $this->telegram::sendMessage([
            "chat_id" => $this->chatId,
            'text' => 'Введiть бажану кiлькiсть товару:',
            'reply_markup' => json_encode(['force_reply' => false, 'selective' => true])
        ]);
        TelegramService::setNextAction("insert", [
                'insert_message_id' => $message->messageId,
                'callbackQuery' => $this->query,
                'callbackQueryParams' => $this->params
            ]
        );
    }

    private function finish()
    {
        $this->telegram::deleteMessage(['message_id' => $this->messageId, 'chat_id' => $this->chatId]);

        $text = TelegramService::cartText();

        if (empty($text)) {
            return $this->telegram::editMessageText([
                'chat_id' => $this->chatId,
                'message_id' => cache()->get(Auth::user()->id . TelegramService::CART_EDIT_MESSAGE_ID_KEY),
                'text' => 'У кошику нічого немає'
            ]);
        }

        return $this->telegram::editMessageText([
            'chat_id' => $this->chatId,
            'message_id' => cache()->get(Auth::user()->id . TelegramService::CART_EDIT_MESSAGE_ID_KEY),
            'text' => $text,
            'parse_mode' => 'html',
            'reply_markup' => Keyboard::make([
                'inline_keyboard' => Keyboards::cartKeyboard()
            ])
        ]);
    }
}
