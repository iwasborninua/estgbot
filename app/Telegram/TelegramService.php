<?php

namespace App\Telegram;

use App\Telegram\Queries\BaseQuery;
use App\Telegram\Queries\Cart\AddToCartQuery;
use App\Telegram\Queries\CategoryQuery;
use App\Telegram\Queries\EmptyQuery;
use App\Telegram\Queries\MenuQuery;
use App\Telegram\Queries\ProductQuery;
use http\Params;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Objects\Update;

class TelegramService
{
    const TEXT_COMMANDS = [
        '📘 Меню' => 'menu',
        '📱 Контакти' => 'contacts',
        '❓ FAQ' => 'faq',
    ];

    private array $queries = [
        'menu' => MenuQuery::class,
        'category' => CategoryQuery::class,
        'empty' => EmptyQuery::class,
        'product' => ProductQuery::class,
        'add-to-cart' => AddToCartQuery::class,
    ];

    public function messageHandler(Update $update)
    {
        if (array_key_exists($update->message->text, self::TEXT_COMMANDS)) {
            return Telegram::triggerCommand(TelegramService::TEXT_COMMANDS[$update->message->text], $update);
        }

        Telegram::processCommand($update);
    }

    public function callbackQueryHandler(Update $update)
    {
        $query = $update->getRelatedObject();

        parse_str($query->data, $params);
        if (array_key_exists('query', $params) and array_key_exists($params['query'], $this->queries)) {
            $class = $this->queryClassCreator($this->queries[$params['query']], $query, $params);
            return $class->handle();
        } else {

            Log::warning('Unknown query key or key is not exist');
        }
    }

    private function queryClassCreator(string $class, ...$params): BaseQuery
    {
        return new ($class)(...$params);
    }


}

