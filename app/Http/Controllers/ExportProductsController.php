<?php

namespace App\Http\Controllers;

use App\Telegram\TelegramServiceInterface;

class ExportProductsController extends Controller
{
    //dispatcher
    public function csv()
    {
        $products = Product::query()->with([
            'category',
            'description' => function ($q) {
                $q->where('language_id', 3);
            },
            'category.description' => function ($q) {
                $q->where('language_id', 3);
            },
            'options.description' => function ($q) {
                $q->where('language_id', 3);
            },
            'options.values',
            'options.values.description'
        ])->where('status', 1)->get();
        $path = __DIR__ . "/csv";
        if (!file_exists($path)) {
            mkdir($path . "/", 0777, true);
        }
        $f = fopen($path . "/product_ru.csv", 'w');
        fprintf($f, chr(0xEF) . chr(0xBB) . chr(0xBF));
        $header = [
            "Категория*",
            "Продукт*",
            'Описание продукта*',
            'Цена*',
            'Количество*',
            'Название варианта 1',
            'Значение варианта 1',
            'Название варианта 2',
            'Значение варианта 2',
            'Название варианта 3',
            'Значение варианта 3',
            'Название варианта 4',
            'Значение варианта 4',
            'Название варианта 5',
            'Значение варианта 5',
            'Название варианта 6',
            'Значение варианта 6',
        ];
        fputcsv($f, $header);
        $i = 0;
        $j = 0;
        foreach ($products as $product) {
            if ($product->options) {
                foreach ($product->category as $category) {
                    foreach ($product->options as $option) {
                        if ($i == 100) {
                            $j += $i;
                            fclose($f);
                            $f = fopen($path . "/product_ru_$j.csv", 'w');
                            fprintf($f, chr(0xEF) . chr(0xBB) . chr(0xBF));
                            fputcsv($f, $header);
                            $i = 0;
                        }
                        $quantity = $product->quantity < 0 ? 0 : $product->quantity;
                        $data = [
                            $category->description[0]->name,
                            $product->description[0]->name,
                            $product->description[0]->description,
                            round($product->price, 2),
                            $quantity,
                        ];
                        foreach ($option->values as $optionValues) {
                            $data[] = $option->description[0]->name;
                            $data[] = $optionValues->description->name;
                        }
                        fputcsv($f, $data);
                        $i++;
                    }
                }
            }
        }
        fclose($f);
    }
}
