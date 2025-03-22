<?php

namespace App\Telegram\Queries;

use App\Models\Category;
use App\Models\CategoryDescription;
use App\Models\ProductToCategory;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Keyboard\Keyboard;
use Telegram\Bot\Objects\CallbackQuery;

class CategoryQuery extends BaseQuery
{
    public static string $name = 'category';

    private $category;

    public function __construct(CallbackQuery $query, array $params)
    {
        parent::__construct($query, $params);

        $this->category = Category::query()->with('description', function ($q) {
            $q->where('language_id', config('constants.lang'));
        })
            ->where('category_id', $this->params['category'])
            ->first();
    }

    public function handle()
    {
        if (key_exists('page', $this->params)) {
            $page = $this->params['page'];
        } else {
            $page = 1;
        }
        $products = $this->category->products()
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
                'callback_data' => 'query=product&product=' . $product->product_id . "&category=" . $this->category->category_id . "&page=$page"
            ]];
        }

        if ($products->currentPage() !== 1) {
            $nav[] = [
                'text' => "⬅️",
                'callback_data' => 'query=category&category=' . $this->category->category_id . "&page=" . $products->currentPage() - 1
            ];
        }

        $nav[] = [
            'text' => $products->currentPage() . "/" . $products->lastPage(),
            'callback_data' => 'query=empty'
        ];

        if ($products->currentPage() != $products->lastPage()) {
            $nav[] = [
                'text' => "➡️",
                'callback_data' => 'query=category&category=' . $this->category->category_id . "&page=" . $products->currentPage() + 1
            ];
        }
        $items[] = $nav;
        $items[] = [['text' => 'Назад', 'callback_data' => "query=menu"]];


        $this->telegram::editMessageText([
            'chat_id' => $this->chatId,
            'message_id' => $this->messageId,
            'text' => $this->category->description[0]?->name,
            'reply_markup' => Keyboard::make([
                'inline_keyboard' => $items
            ])
        ]);
    }
}
