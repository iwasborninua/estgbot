<?php

namespace App\Telegram\Queries\Cart;

use App\Models\Product;
use App\Telegram\Queries\BaseQuery;
use App\Telegram\TelegramService;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Telegram\Bot\FileUpload\InputFile;
use Telegram\Bot\Keyboard\Keyboard;
use Telegram\Bot\Objects\CallbackQuery;
use Telegram\Bot\Objects\InputMedia\InputMedia;

class EditCartQuery extends BaseQuery
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
        if (Arr::has($this->params, 'cache-cart-message')) {
            \Cache::set(Auth::user()->id . TelegramService::CART_EDIT_MESSAGE_ID_KEY, $this->messageId, 600000);
        }

        $cart = Auth::user()->getCart();

        $page = $this->params['page'] ?? 1;
        $productId = array_keys($cart)[$page - 1];
        $optionsValues = $cart[$productId];
        $countCart = count($cart);

        $data = Product::productDataForEdit($productId, $optionsValues);
        $caption = "<b>{$data->description[0]->name}</b>" . PHP_EOL . PHP_EOL;
        $items = [];
        foreach ($data->options as $option) {
            foreach ($option->values as $value) {
                $price = $data->price + $value->price;
                $count = $cart[$data->product_id][$value->product_option_value_id];
                $sum = $count * $price;
                $caption .= $value->description->name . ": " . $count . "шт. x " . $price . "₴ = " . $sum . "₴" . PHP_EOL;

                if (Arr::has($this->params, 'pov') and $this->params['pov'] == $value->product_option_value_id) {
                    $item = [
                        [
                            'text' => "-1",
                            'callback_data' => "query=update-cart&product=$productId&pov={$value->product_option_value_id}&method=minus&page=$page"
                        ],
                        [
                            'text' => "✏️ $count шт. | {$price}₴ {$value->description->name}",
                            'callback_data' => "query=update-cart&product=$productId&pov={$value->product_option_value_id}&method=insert&page=$page"
                        ],
                        [
                            'text' => "+1",
                            'callback_data' => "query=update-cart&product=$productId&pov={$value->product_option_value_id}&method=plus&page=$page"
                        ],
                    ];
                } else {
                    $item = [[
                        'text' => "✏️ $count шт. | {$price}₴ {$value->description->name}",
                        'callback_data' => "query=edit-cart&pov={$value->product_option_value_id}&page=$page&next-edit=1"
                    ]];
                }
                $items[] = $item;
            }
        }
        if ($countCart !== 1) {
            $nextPage = ($page + 1) > $countCart ? 1 : $page + 1;
            $prevPage = ($page - 1) == 0 ? $countCart : $page - 1;
            $items[] = [
                [
                    'text' => "⬅️",
                    'callback_data' => "query=edit-cart&page=$prevPage&next-edit=1"
                ],
                [
                    'text' => "$page/$countCart",
                    'callback_data' => 'query=empty'
                ],
                [
                    'text' => "➡️",
                    'callback_data' => "query=edit-cart&page=$nextPage&next-edit=1"
                ],
            ];
        }
        $items[] = [[
            'text' => '✅ Завершити редагування',
            'callback_data' => "query=update-cart&method=finish"
        ]];
        if (Arr::has($this->params, 'next-edit')) {
            $this->telegram::editMessageMedia([
                'chat_id' => $this->chatId,
                'message_id' => $this->messageId,
                'media' => json_encode(new InputMedia([
                    'type' => 'photo',
                    'media' => 'https://api.errors-seeds.com.ua/image/' . $data->image,
                    'parse_mode' => 'html',
                    'caption' => $caption,
                ])),
                'reply_markup' => Keyboard::make([
                    'inline_keyboard' => $items
                ])
            ]);
        } else {
            $this->telegram::sendPhoto([
                'chat_id' => $this->chatId,
                'photo' => InputFile::create('https://api.errors-seeds.com.ua/image/' . $data->image),
                'caption' => $caption,
                'parse_mode' => 'html',
                'reply_markup' => Keyboard::make([
                    'inline_keyboard' => $items
                ])
            ]);
        }
    }
}
