<?php

namespace App\Telegram;

use App\Models\User;
use App\Telegram\Queries\BaseQuery;
use App\Telegram\Queries\Cart\AddToCartQuery;
use App\Telegram\Queries\CategoryQuery;
use App\Telegram\Queries\EmptyQuery;
use App\Telegram\Queries\MenuQuery;
use App\Telegram\Queries\ProductQuery;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Objects\Update;

class TelegramService implements TelegramServiceInterface
{
    private string $type;

    public function __construct(private Update $update)
    {
        $this->type = str_replace("_", "", ucwords($this->update->objectType(), " _"));
    }

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
        'atc' => AddToCartQuery::class,
    ];

    public function handleUpdate()
    {

        try {
            $method = $this->type . "Handler";
            if (method_exists($this, $method)) {
                return $this->{$method}($this->update);
            } else {
                Log::error("Unknown object Type[{$this->type}]. No Handle method");
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }

    }

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

    public function authUser()
    {
        $data = $this->update->getRelatedObject()->from;
        $user = User::query()->updateOrCreate(
            ['username' => $data->username],
            $data->toArray()
        );
        Auth::login($user);
    }

}

