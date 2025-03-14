<?php

namespace App\Telegram;

use App\Models\Product;
use App\Models\ProductDescription;
use App\Models\ProductOptionValue;
use App\Models\User;
use App\Telegram\Handlers\Handler;
use App\Telegram\Queries\BaseQuery;
use App\Telegram\Queries\Cart\AddToCartQuery;
use App\Telegram\Queries\Cart\DeleteCartQuery;
use App\Telegram\Queries\Cart\EditCartQuery;
use App\Telegram\Queries\Cart\OrderQuery;
use App\Telegram\Queries\Cart\UpdateCartQuery;
use App\Telegram\Queries\CategoryQuery;
use App\Telegram\Queries\EmptyQuery;
use App\Telegram\Queries\MenuQuery;
use App\Telegram\Queries\Order\ConfirmQuery;
use App\Telegram\Queries\Order\GetNumberQuery;
use App\Telegram\Queries\Order\PostQuery;
use App\Telegram\Queries\Order\RepeatNumberQuery;
use App\Telegram\Queries\Order\SelectFIOQuery;
use App\Telegram\Queries\Order\SelectPaymentQuery;
use App\Telegram\Queries\Order\SelectPostQuery;
use App\Telegram\Queries\Order\TotalQuery;
use App\Telegram\Queries\ProductQuery;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Objects\Update;

class TelegramService implements TelegramServiceInterface
{
    const CART_EDIT_MESSAGE_ID_KEY = 'CART_EDIT_MESSAGE_ID_KEY';
    const NEXT_ACTION = 'NEXT_ACTION';
    const NEXT_ACTION_PARAMS = 'NEXT_ACTION_PARAMS';

    const NOVAPOST = 'novapost';
    const UKRPOST = 'ukrpost';
    const UKRPOST_KZ = 'ukrpost_kz';

    const OVERHEAD_PAYMENT = 'overhead-payment';
    const PAID = 'paid';
    const CANCEL = 'Відміна';
    private string $type;

    public function __construct(private Update $update)
    {
        $this->type = str_replace("_", "", ucwords($this->update->objectType(), " _"));
    }

    const TEXT_COMMANDS = [
        '📘 Меню' => 'menu',
        '📱 Контакти' => 'contacts',
        '❓ FAQ' => 'faq',
        '🛒 Корзина' => 'cart',
    ];

    private array $queries = [
        'menu' => MenuQuery::class,
        'category' => CategoryQuery::class,
        'empty' => EmptyQuery::class,
        'product' => ProductQuery::class,
        'atc' => AddToCartQuery::class,
        'edit-cart' => EditCartQuery::class,
        'update-cart' => UpdateCartQuery::class,
        'delete-cart' => DeleteCartQuery::class,
        'make-order' => OrderQuery::class,
        'post' => PostQuery::class,
        'select-post' => SelectPostQuery::class,
        'select-fio' => SelectFIOQuery::class,
        'select-payment' => SelectPaymentQuery::class,
        'get-number' => GetNumberQuery::class,
        'repeat-number' => RepeatNumberQuery::class,
        'total' => TotalQuery::class,
        'confirm-order' => ConfirmQuery::class,
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
            $this->clearNextAction();
            return Telegram::triggerCommand(TelegramService::TEXT_COMMANDS[$update->message->text], $update);
        }
        if ($this->hasNextAction()) {
            return $this->handleNextAction();
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


    public static function cartText()
    {
        $cart = Auth::user()->getCart();

        if (empty($cart)) return "";

        $total = 0;
        $text = "<b>Кошик</b>" . PHP_EOL . PHP_EOL . "-----" . PHP_EOL;
        foreach ($cart as $productId => $options) {
            $productPrice = Product::query()->where('product_id', $productId)->value('price');
            $name = ProductDescription::query()
                ->where('product_id', $productId)
                ->where('language_id', config('constants.lang'))
                ->value('name');

            $text .= "<b>$name</b>" . PHP_EOL;

            $optionsData = ProductOptionValue::query()
                ->with('description', function ($q) {
                    $q->select(['name', 'option_value_id']);
                })
                ->whereIn('product_option_value_id', array_keys($options))
                ->get(['product_option_value_id', 'quantity', 'option_value_id', 'price']);

            foreach ($optionsData as $data) {
                $price = $productPrice + $data->price;
                $count = $options[$data->product_option_value_id];
                $sum = $count * $price;
                $total += $sum;
                $text .= $data->description->name . ": " . $count . "шт. x " . $price . "₴ = " . $sum . "₴" . PHP_EOL;
            }
            $text .= PHP_EOL;
        }
        $text .= "-----" . PHP_EOL . "<b>Загалом:</b> {$total}₴";

        return $text;
    }

    public function handleNextAction()
    {
        $actonHandler = app(Handler::class);
        $method = cache()->get(Auth::id() . $this::NEXT_ACTION);
        $this->clearNextAction();
        return $actonHandler->{$method}();
    }

    public function hasNextAction(): bool
    {
        return cache()->has(Auth::id() . $this::NEXT_ACTION);
    }

    public static function setNextAction(string $method, array $params = []): void
    {
        cache()->set(Auth::id() . self::NEXT_ACTION, $method);
        cache()->set(Auth::id() . self::NEXT_ACTION_PARAMS, $params);
    }

    public function clearNextAction(): void
    {
        cache()->forget(Auth::id() . self::NEXT_ACTION);
    }
}

