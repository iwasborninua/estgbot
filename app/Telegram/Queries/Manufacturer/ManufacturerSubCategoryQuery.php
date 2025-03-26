<?php

namespace App\Telegram\Queries\Manufacturer;

use App\Models\Category;
use App\Models\CategoryDescription;
use App\Models\Manufacturer;
use App\Telegram\Keyboards\Keyboards;
use App\Telegram\Queries\BaseQuery;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Keyboard\Keyboard;
use Telegram\Bot\Objects\CallbackQuery;

class ManufacturerSubCategoryQuery extends BaseQuery
{
    public static string $name = 'menu';

    public function __construct(CallbackQuery $query, array $params)
    {
        parent::__construct($query, $params);
    }

    public function handle()
    {
        if (key_exists('page', $this->params)) {
            $page = $this->params['page'];
        } else {
            $page = 1;
        }
        $manufacturers = Manufacturer::menuQueryCat($page, $this->params['category']);
        $name = CategoryDescription::query()
            ->where('language_id', config('constants.lang'))
            ->where('category_id', $this->params['category'])
            ->value('name');
        $i = 1;
        $rowCount = 2;
        $count = count($manufacturers) - 1;
        $items = $rowData = [];
        foreach ($manufacturers as $k => $manufacturer) {
            $rowData[] = [
                'text' => $manufacturer->name . " ({$manufacturer->products_count})",
                'callback_data' => "query=manufacturer&manufacturer={$manufacturer->manufacturer_id}" . "&page=$page"
                    . '&category=' . $this->params['category']
            ];
            if ($i === $rowCount or $k === $count) {
                $items[] = $rowData;
                $i = 1;
                $rowData = [];
            } else {
                $i++;
            }
        }
        if ($manufacturers->currentPage() !== 1) {
            $nav[] = [
                'text' => "⬅️",
                'callback_data' => 'query=sub-manufacturer' . '&category=' . $this->params['category'] . "&page=" . $manufacturers->currentPage() - 1
            ];
        }

        $nav[] = [
            'text' => $manufacturers->currentPage() . "/" . $manufacturers->lastPage(),
            'callback_data' => 'query=empty'
        ];

        if ($manufacturers->currentPage() != $manufacturers->lastPage()) {
            $nav[] = [
                'text' => "➡️",
                'callback_data' => 'query=sub-manufacturer' . '&category=' . $this->params['category'] . "&page=" . $manufacturers->currentPage() + 1
            ];
        }
        $items[] = $nav;
        $items[] = [['text' => 'Назад', 'callback_data' => "query=menu"]];

        $this->telegram::editMessageText([
            'chat_id' => $this->chatId,
            'message_id' => $this->messageId,
            'text' => "Виробники в категорії $name",
            'reply_markup' => Keyboard::make([
                'inline_keyboard' => $items
            ])
        ]);
    }
}
