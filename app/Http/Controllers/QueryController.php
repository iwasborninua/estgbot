<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\CategoryDescription;
use App\Models\ProductToCategory;
use App\Telegram\Keyboards\Keyboards;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Keyboard\Keyboard;
use Telegram\Bot\Laravel\Facades\Telegram;

class QueryController extends Controller
{

    private Telegram $telegram;
    private mixed $chatId;
    private $message_id;

    public function __construct(private $update)
    {
        $this->telegram = new Telegram();
        $this->chatId = $this->update['callback_query']['message']['chat']['id'];
        $this->message_id = $this->update['callback_query']['message']['message_id'];
    }

    public function handle()
    {
        $data = $this->update->getRelatedObject()->data;


        if ($data === "/menu") {
            return $this->getBackToMenu();

        }

        if (str_starts_with($data, 'category')) {
            return $this->handleCategory($data);
        }

    }

    private function handleCategory($data)
    {
        parse_str($data, $params);
        if (key_exists('page', $params)) {
            $page = $params['page'];
        } else {
            $page = 1;
        }
        Log::info(print_r($params, 1));
        $category = CategoryDescription::query()->where('category_id', $params['category'])->where('language_id', 3)->first();
        $data = ProductToCategory::query()
            ->select(['category_id', 'product_id',])
            ->with(['category' => function ($q) {
                $q->select('category_id');
            }, 'product' => function ($q) {
                $q->select(['product_id', 'quantity', 'price']);
            }, 'product.ukrDescription' => function ($q) {
                $q->select(['product_id', 'name']);
            }])
            ->whereHas('category', function ($q) {
                $q->where('status', 1)->where('main_category', 1);
            })
            ->whereHas('product', function ($q) {
                $q->where('quantity', '>', 0);
            })
            ->where('category_id', $category->category_id)
            ->paginate(10, ["*"], 'page', $page);

        $items = [];
        $nav = [];
        foreach ($data as $d) {
            $items[] = [[
                'text' => $d->product->ukrDescription->name . ' вiд ' . round($d->product->price) . '₴',
                'callback_data' => 'product=' . $d->product_id
            ]];
        }

        if ($data->currentPage() !== 1) {
            $nav[] = ['text' => "Попередня сторінка", 'callback_data' => 'category=' . $category->category_id . "&page=" . $data->currentPage() - 1];
        }

        $nav[] = ['text' => $data->currentPage() . "/" . $data->lastPage(), 'callback_data' => 'dwadwadaw='];

        if ($data->currentPage() != $data->lastPage()) {
            $nav[] = ['text' => "Наступна сторінка", 'callback_data' => 'category=' . $category->category_id . "&page=" . $data->currentPage() + 1];
        }
        $items[] = $nav;
        $items[] = [['text' => 'Назад', 'callback_data' => "/menu"]];

        $this->telegram::editMessageText([
            'chat_id' => $this->chatId,
            'message_id' => $this->message_id,
            'text' => $category->name,
            'reply_markup' => Keyboard::make([
                'inline_keyboard' => $items
            ])
        ]);
    }

    private function getBackToMenu()
    {
        $this->telegram::editMessageText([
            'chat_id' => $this->chatId,
            'message_id' => $this->message_id,
            'text' => "Ось що у нас є:",
            'reply_markup' => Keyboard::make([
                'inline_keyboard' => Keyboards::catalogKeyboards()
            ])
        ]);
    }


}
