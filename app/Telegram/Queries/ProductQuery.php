<?php

namespace App\Telegram\Queries;

use App\Models\Category;
use App\Models\Product;
use App\Telegram\Keyboards\Keyboards;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Psy\Util\Json;
use Telegram\Bot\FileUpload\InputFile;
use Telegram\Bot\Keyboard\Keyboard;
use Telegram\Bot\Objects\CallbackQuery;
use Telegram\Bot\Objects\InputMedia\InputMedia;
use Telegram\Bot\Objects\InputMedia\InputMediaPhoto;

class ProductQuery extends BaseQuery
{
    public static string $name = 'product';

    private Product $product;

    public function __construct(CallbackQuery $query, array $params)
    {
        parent::__construct($query, $params);

        $this->product = Product::find($params['product']);
    }

    public function handle()
    {
        $this->telegram::editMessageMedia([
            'chat_id' => $this->chatId,
            'message_id' => $this->messageId,
            'media' => json_encode(new InputMedia([
                'type' => 'photo',
                'media' => url('image/' . $this->product->image),
//                'media' => "https://api.errors-seeds.com.ua/image/catalog/product_images/errors_seeds_gold/glato-feminised-gold.jpg",
                'parse_mode' => 'html',
                'caption' => "Ось що у нас є:",
            ])),

            'reply_markup' => Keyboard::make([
                'inline_keyboard' => [[['text' => 'Назад', 'callback_data' =>"deletemessage=1&query=category&category=" . $this->params['category']]]]
            ])
        ]);
    }
}
