<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use GuzzleHttp\Client;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;


    protected $table = 'tg_users';
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    protected $fillable = [
        'id',
        'username',
        'first_name',
        'last_name',
        'language_code',
        'is_bot',
        'chat_id',
    ];

    public function getCart()
    {
        if (!cache()->has($this->id . 'cart')) {
            return [];
        }
        return cache()->get($this->id . 'cart');
    }

    public function setCart($cart)
    {
        cache()->set($this->id . 'cart', $cart, 60000);
    }

    public function getDelivery()
    {
        if (!cache()->has($this->id . 'delivery')) {
            return [];
        }
        return cache()->get($this->id . 'delivery');
    }

    public function setDelivery(array $delivery)
    {
        cache()->set($this->id . 'delivery', $delivery, 60000);
    }

    public function createOrder()
    {
        $delivery = $this->getDelivery();
        $cart = $this->getCart();
        $order_data = [];
        $orderTotal = 0;

        foreach ($cart as $productId => $options) {
            $productQuery = Product::query()
                ->with('description', function ($q) {
                    $q->select(['product_id', 'name'])->where('language_id', config('constants.lang'));
                })
                ->where('product_id', $productId)->first();
            $optionsQuery = ProductOptionValue::query()
                ->with('special', function ($q) use ($productQuery){
                    $q->where('special_id', $productQuery->special?->product_special_id);
                })
                ->with(['description', 'option', 'option.description'])
                ->whereIn('product_option_value_id', array_keys($options))
                ->get(['product_option_value_id', 'quantity', 'option_value_id', 'price', 'product_option_id', 'option_id']);

            foreach ($optionsQuery as $data) {
                $option_data = [];
                $price = $productQuery->price() + $data->price();
                $quantity = $options[$data->product_option_value_id];
                $total = $quantity * $price;
                $orderTotal += $total;

                $option_data[] = [
                    'product_option_id' => $data->product_option_id,
                    'product_option_value_id' => $data->product_option_value_id,
                    'option_id' => $data->option_id,
                    'option_value_id' => $data->option_value_id,
                    'name' => $data->option->description->name,
                    'value' => $data->description->name,
                    'type' => $data->option->type,
                ];
                $product_data = [
                    'product_id' => $productId,
                    'name' => $productQuery->description[0]->name,
                    'model' => $productQuery->model,
                    'option' => $option_data,
                    'quantity' => $quantity,
                    'price' => $price,
                    'total' => $total,
                    'tax' => 0,
                    'reward' => 0
                ];
                $order_data['products'][] = $product_data;
            }
        }
        $order_data['total'] = $orderTotal;
        $order_data['totals'][] = ['code' => 'sub_total', 'title' => 'Підсумок', 'value' => $orderTotal, 'sort_order' => 0];
        $order_data['totals'][] = ['code' => 'total', 'title' => 'Всього', 'value' => $orderTotal, 'sort_order' => 9];

        $name = explode(' ', $delivery['fio']);
        $city = explode(',', $delivery['city']);
        //

        $order_data['comment'] = 'FROM TG';
        $order_data['language_id'] = config('constants.lang');
        $order_data['currency_id'] = 1;
        $order_data['currency_code'] = 'UAH';
        $order_data['currency_value'] = 1;

        $order_data['invoice_prefix'] = 'TG-';
        $order_data['store_id'] = 0;
        $order_data['store_name'] = 'TG';
        $order_data['store_url'] = 'TG';

        $order_data['customer_id'] = $this->id;
        $order_data['customer_group_id'] = 1;
        $order_data['firstname'] = $name[1];
        $order_data['lastname'] = $name[0];
        $order_data['email'] = 'order-from-tg@email.net';
        $order_data['telephone'] = $delivery['phone'];
        $order_data['telephone_country_id'] = 0;
        $order_data['fax'] = 0;
        $order_data['payment_company'] = '';
        $order_data['payment_address_2'] = '';
        $order_data['payment_country'] = '';
        $order_data['payment_country_id'] = '';
        $order_data['payment_zone_id'] = '';
        $order_data['payment_address_format'] = '';
        $order_data['shipping_company'] = '';
        $order_data['shipping_address_2'] = '';
        $order_data['shipping_country_id'] = '';
        $order_data['shipping_zone_id'] = '';
        $order_data['shipping_address_format'] = '';
        $order_data['affiliate_id'] = '';
        $order_data['commission'] = '';
        $order_data['marketing_id'] = '';
        $order_data['tracking'] = '';
        $order_data['ip'] = 'TG';
        $order_data['user_agent'] = 'TG';
        $order_data['forwarded_ip'] = 'TG';
        $order_data['accept_language'] = 'ru-RU,ru;q=0.9,en-US;q=0.8,en;q=0.7,uk;q=0.6';
        $order_data['newsletter'] = 0;
        $order_data['gifts'] = 0;

        $order_data['payment_firstname'] = $name[1];
        $order_data['payment_lastname'] = $name[0];
        $order_data['payment_address_1'] = $delivery['postData'];
        $order_data['payment_city'] = $city[0];
        $order_data['payment_zone'] = $city[1];
        $order_data['payment_method'] = $delivery['payment'] == 'paid' ? 'оплата на особовий рахунок' : "Накладна плата";
        $order_data['payment_code'] = $delivery['payment'] == 'paid' ? 'pb_card' : "cod";

        $order_data['shipping_firstname'] = $name[1];
        $order_data['shipping_lastname'] = $name[0];
        $order_data['shipping_address_1'] = $delivery['postData'];
        $order_data['shipping_city'] = $city[0];
        $order_data['shipping_zone'] = $city[1];
        $order_data['shipping_postcode'] = $delivery['postData'];
        $order_data['shipping_method'] = config('delivery.post.' . $delivery['post']);
        $order_data['shipping_code'] = $delivery['post'];
        $order_data['shipping_country'] = "Ukraine";


        $res = Http::asForm()->post(env("ERROR_URL"), ['data' => $order_data, 'key' => env('ERROR_SECRET')]);
        if ($res->ok()) {
            $this->setCart([]);
            $this->setDelivery([]);
            return $res->body();
        }
        return 'ERROR';
    }


    public function getCartTotal()
    {
        $cart = $this->getCart();
        $total = 0;

        foreach ($cart as $productId => $options) {
            $productQuery = Product::query()
                ->with('description', function ($q) {
                    $q->select(['product_id', 'name'])->where('language_id', config('constants.lang'));
                })
                ->where('product_id', $productId)->first();
            $optionsQuery = ProductOptionValue::query()
                ->with('special', function ($q) use ($productQuery){
                    $q->where('special_id', $productQuery->special?->product_special_id);
                })
                ->with(['description', 'option', 'option.description'])
                ->whereIn('product_option_value_id', array_keys($options))
                ->get(['product_option_value_id', 'quantity', 'option_value_id', 'price', 'product_option_id', 'option_id']);

            foreach ($optionsQuery as $data) {
                $price = $productQuery->price() + $data->price();
                $quantity = $options[$data->product_option_value_id];
                $total += ($quantity * $price);
            }
        }

        return $total;
    }
}
