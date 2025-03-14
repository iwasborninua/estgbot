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

class DeleteCartQuery extends BaseQuery
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
        Auth::user()->setCart([]);

        $this->telegram::editMessageText([
            'chat_id' => $this->chatId,
            'message_id' => $this->messageId,
            'text' => 'У кошику нічого немає'
        ]);
    }

}
