<?php

namespace App\Telegram\Queries\Manufacturer;

use App\Models\CategoryDescription;
use App\Models\Manufacturer;
use App\Models\ProductToCategory;
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
            ->where('quantity', '>', 0);
        if (\Arr::has($this->params, 'category')) {
            $products = $products->whereHas('category', function ($q) {
                $q->where('oc_product_to_category.category_id', $this->params['category']);
            });
        }
        $products = $products->paginate(10, ["*"], 'page', $page);

        if (\Arr::has($this->params, 'category')) {
            $andCategory = "&category=" . $this->params['category'];
        } else {
            $andCategory = '';
        }

        $items = [];
        $nav = [];


        foreach ($products as $product) {
            $items[] = [[
                'text' => ($product->inCart() ? "(" . $product->inCartCount() . ") " : "") .
                    $product->description[0]->name . ' вiд ' . round($product->price) . '₴',
                'callback_data' => 'query=product&product=' . $product->product_id . "&manufacturer=" . $this->manufacturer->manufacturer_id .
                    "&page=$page" . $andCategory
            ]];
        }

        if ($products->currentPage() !== 1) {
            $nav[] = [
                'text' => "⬅️",
                'callback_data' => 'query=manufacturer&manufacturer=' . $this->manufacturer->manufacturer_id . "&page=" .
                    $products->currentPage() - 1 . $andCategory
            ];
        }

        $nav[] = [
            'text' => $products->currentPage() . "/" . $products->lastPage(),
            'callback_data' => 'query=empty'
        ];

        if ($products->currentPage() != $products->lastPage()) {
            $nav[] = [
                'text' => "➡️",
                'callback_data' => 'query=manufacturer&manufacturer=' . $this->manufacturer->manufacturer_id . "&page=" .
                    $products->currentPage() + 1 . $andCategory
            ];
        }
        $items[] = $nav;
        if (\Arr::has($this->params, 'category')) {
            $items[] = [[
                'text' => "Назад",
                'callback_data' => 'query=sub-manufacturer' . '&category=' . $this->params['category']
            ]];
        } else {
            $items[] = [['text' => 'Назад', 'callback_data' => "query=menu"]];
        }

        if (\Arr::has($this->params, 'category')) {
            $name = CategoryDescription::query()
                    ->where('language_id', config('constants.lang'))
                    ->where('category_id', $this->params['category'])
                    ->value('name') . ' - ' . $this->manufacturer->name;
        } else {
            $name = $this->manufacturer->name;
        }

        $this->telegram::editMessageText([
            'chat_id' => $this->chatId,
            'message_id' => $this->messageId,
            'text' => $name,
            'reply_markup' => Keyboard::make([
                'inline_keyboard' => $items
            ])
        ]);
    }
}
