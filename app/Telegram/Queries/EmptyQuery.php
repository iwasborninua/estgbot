<?php

namespace App\Telegram\Queries;

use Illuminate\Support\Facades\Log;
use Telegram\Bot\Objects\CallbackQuery;

class EmptyQuery extends BaseQuery
{
    public static string $name = 'menu';

    public function __construct(CallbackQuery $query, array $params)
    {
        parent::__construct($query, $params);
    }

    public function handle()
    {
        //
    }
}
