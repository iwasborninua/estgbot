<?php

namespace App\Telegram\Queries\Manufacturer;

use App\Models\Manufacturer;
use App\Telegram\Queries\BaseQuery;
use Telegram\Bot\Keyboard\Keyboard;
use Telegram\Bot\Objects\CallbackQuery;

class ManufacturerQuery extends BaseQuery
{
    public static string $name = 'manufacturer';

    private $manufacturer;

    public function __construct(CallbackQuery $query, array $params)
    {
        parent::__construct($query, $params);

        $this->manufacturer = Manufacturer::query()->where('manufacturer_id', $this->params['manufacturer'])
            ->first();
    }

    public function handle()
    {
        if (key_exists('page', $this->params)) {
            $page = $this->params['page'];
        } else {
            $page = 1;
        }
        $products = $this->manufacturer->products()
            ->select(['oc_product.product_id', 'price'])
            ->with('description', function ($q) {
                $q->select(['product_id', 'name'])->where('language_id', config('constants.lang'));
            })
            ->where('quantity', '>', 0)
            ->paginate(10, ["*"], 'page', $page);

        $items = [];
        $nav = [];
        foreach ($products as $product) {
            $items[] = [[
                'text' => ($product->inCart() ? "(" . $product->inCartCount() . ") " : "") .
                    $product->description[0]->name . ' вiд ' . round($product->price) . '₴',
                'callback_data' => 'query=product&product=' . $product->product_id . "&manufacturer=" . $this->manufacturer->manufacturer_id . "&page=$page"
            ]];
        }

        if ($products->currentPage() !== 1) {
            $nav[] = [
                'text' => "⬅️",
                'callback_data' => 'query=manufacturer&manufacturer=' . $this->manufacturer->manufacturer_id . "&page=" . $products->currentPage() - 1
            ];
        }

        $nav[] = [
            'text' => $products->currentPage() . "/" . $products->lastPage(),
            'callback_data' => 'query=empty'
        ];

        if ($products->currentPage() != $products->lastPage()) {
            $nav[] = [
                'text' => "➡️",
                'callback_data' => 'query=manufacturer&manufacturer=' . $this->manufacturer->manufacturer_id . "&page=" . $products->currentPage() + 1
            ];
        }
        $items[] = $nav;
        $items[] = [['text' => 'Назад', 'callback_data' => "query=menu"]];


        $this->telegram::editMessageText([
            'chat_id' => $this->chatId,
            'message_id' => $this->messageId,
            'text' => $this->manufacturer->name,
            'reply_markup' => Keyboard::make([
                'inline_keyboard' => $items
            ])
        ]);
    }
}
