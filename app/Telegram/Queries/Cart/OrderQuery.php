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

class OrderQuery extends BaseQuery
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
        $message = $this->telegram::editMessageText([
            "chat_id" => $this->chatId,
            "message_id" => $this->messageId,
            'text' => "Куди доставляти?" . PHP_EOL . "(Напишіть ваше місто та область)",
        ]);
        TelegramService::setNextAction("orderCity", [
                'prev_message' => $message->messageId,
                'callbackQuery' => $this->query,
                'callbackQueryParams' => $this->params
            ]
        );
    }
}
