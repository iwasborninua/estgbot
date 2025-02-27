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
        $text = PHP_EOL;
        $items = [];
        $description = $this->product->description()->where('language_id', config('constants.lang'))->first();
        $attributes = $this->product->attributes()->with(['attributeDescription'])->where('language_id', 3)->get(['text', 'attribute_id']);
        $options = $this->product->options()
            ->with('values', function ($q) {
                $q->select(['product_option_id', 'quantity', 'price', 'option_value_id']);
            })
            ->with('values.description')
            ->get(['product_id', 'product_option_id']);

        foreach ($attributes as $attribute) {
            $text .= $attribute->attributeDescription->name . ": " . $attribute->text . PHP_EOL;
        }

        foreach ($options as $option) {
            foreach ($option->values->sortBy('quantity') as $value) {
                $price = $this->product->price + $value->price;
                $items[] = [[
                    'text' => "Придбати {$value->description->name} за $price",
                    'callback_data' => "query=add-to-cart&category=" . $this->params['category'] . "&page=" . $this->params['page'] .
                        '&productOptionValue=' . $value->option_value_id
                ]];
            }
        }

        $items[] = [[
            'text' => 'Назад',
            'callback_data' => "query=category&category=" . $this->params['category'] . "&page=" . $this->params['page']
        ]];
//        $this->telegram::editMessageMedia([
//            'chat_id' => $this->chatId,
//            'message_id' => $this->messageId,
//            'media' => json_encode(new InputMedia([
//                'type' => 'photo',
////                'media' => url('image/' . $this->product->image),
//                'media' => "https://api.errors-seeds.com.ua/image/catalog/product_images/errors_seeds_gold/glato-feminised-gold.jpg",
//                'parse_mode' => 'html',
//                'caption' => $description->name,
//            ])),
//
//            'reply_markup' => Keyboard::make([
//                'inline_keyboard' => [[['text' => 'Назад',
//                    'callback_data' =>"deletemessage=1&query=category&category=" . $this->params['category']]]]
//            ])
//        ]);
        $href = url('image/' . $this->product->image);
        $this->telegram::editMessageText([
            'chat_id' => $this->chatId,
            'message_id' => $this->messageId,
            'text' => "<a href='$href'>$description->name</a> $text",
            'parse_mode' => 'HTML',
            'link_preview_options' => json_encode(['url' => $href, 'prefer_small_media' => true]),
            'reply_markup' => Keyboard::make([
                'inline_keyboard' => $items
            ])
        ]);
    }
}
