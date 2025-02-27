<?php

namespace App\Telegram\Queries\Cart;

use App\Telegram\Queries\BaseQuery;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Objects\CallbackQuery;

class AddToCartQuery extends BaseQuery
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
        Log::info($this->telegram);
        $this->telegram::answerCallbackQuery(['text' => "Додано до кошика (у розробцi)", 'callback_query_id' => $this->callbackQueryId]);
    }
}
