<?php

namespace App\Telegram\Handlers;

use App\Telegram\Keyboards\Keyboards;
use App\Telegram\Queries\Cart\EditCartQuery;
use App\Telegram\TelegramService;
use Telegram\Bot\Keyboard\Keyboard;
use Telegram\Bot\Objects\Update;

class ActionHandler implements Handler
{
    private $params;

    public function __construct(private readonly Update $update)
    {
        $this->params = cache(\Auth::id() . TelegramService::NEXT_ACTION_PARAMS);
    }

    public function insert()
    {
        $message = $this->update->message;

        if (is_numeric($message->text)) {
            $cart = \Auth::user()->getCart();
            $cart[$this->params['callbackQueryParams']['product']][$this->params['callbackQueryParams']['pov']] = $message->text;
            \Auth::user()->setCart($cart);

            $query = new EditCartQuery($this->params['callbackQuery'], $this->params['callbackQueryParams']);
            $query->handle();

            \Telegram::editMessageText([
                'chat_id' => $message->chat->id,
                'message_id' => $this->params['insert_message_id'],
                'text' => 'Добре :)'
            ]);
            sleep(2);
            \Telegram::deleteMessage(['chat_id' => $message->chat->id, 'message_id' => $message->messageId]);
            \Telegram::deleteMessage(['chat_id' => $message->chat->id, 'message_id' => $this->params['insert_message_id']]);

        } else {
            \Telegram::deleteMessage(['chat_id' => $message->chat->id, 'message_id' => $message->messageId]);
            try {
                \Telegram::editMessageText([
                    'chat_id' => $message->chat->id,
                    'message_id' => $this->params['insert_message_id'],
                    'text' => 'Кiлькiсть товару повинна бути числом.'
                ]);
                TelegramService::setNextAction('insert', $this->params);

            } catch (\Exception $e) {
                TelegramService::setNextAction('insert', $this->params);

            }
        }
    }

    public function orderCity()
    {
        $message = $this->update->message;
        if (count(explode(',', $message->text)) == 2) {
            $delivery['city'] = $message->text;
            \Auth::user()->setDelivery($delivery);

            \Telegram::deleteMessage([
                'chat_id' => $message->chat->id,
                'message_id' => $this->params['prev_message']
            ]);

            \Telegram::sendMessage([
                "chat_id" => $message->chat->id,
                'text' => 'Чим доставляти?',
                'reply_markup' => Keyboard::make([
                    'inline_keyboard' => [
                        [
                            [
                                'text' => "Нова Пошта",
                                'callback_data' => "query=post&post=" . TelegramService::NOVAPOST
                            ],
                            [
                                'text' => "Укрпошта",
                                'callback_data' => "query=post&post=" . TelegramService::UKRPOST
                            ]
                        ],
//                    [
//                        [
//                            'text' => "Укрпошта (Казахстан)",
//                            'callback_data' => "query=post&post=" . TelegramService::UKRPOST_KZ
//                        ],
//                    ],
                        [
                            [
                                'text' => "Назад",
                                'callback_data' => "query=make-order"
                            ],
                        ]
                    ]
                ])
            ]);
        } else {
            \Telegram::deleteMessage(['chat_id' => $message->chat->id, 'message_id' => $message->messageId]);
            try {
                \Telegram::editMessageText([
                    'chat_id' => $message->chat->id,
                    'message_id' => $this->params['prev_message'],
                    'text' => 'Напишіть ваше місто та область через кому. ' . PHP_EOL . '(приклад <b>Полтава, Полтавська область</b>)',
                    'parse_mode' => 'HTML'
                ]);
                TelegramService::setNextAction('orderCity', $this->params);

            } catch (\Exception $e) {
                TelegramService::setNextAction('orderCity', $this->params);
            }
        }

    }

    public function postData()
    {
        $message = $this->update->message;
        $delivery = \Auth::user()->getDelivery();

        $delivery['postData'] = $message->text;

        \Auth::user()->setDelivery($delivery);
        \Telegram::deleteMessage([
            'chat_id' => $message->chat->id,
            'message_id' => $this->params['prev_message']
        ]);

        $message = \Telegram::sendMessage([
            "chat_id" => $message->chat->id,
            'text' => "Напишіть ім'я та прізвище одержувача посилки",
            'reply_markup' => Keyboard::make(['inline_keyboard' =>
                [[['text' => "Назад", 'callback_data' => "query=post&post={$delivery['post']}"]]]])

        ]);

        TelegramService::setNextAction('fio', ['prev_message' => $message->messageId]);
    }

    public function fio()
    {
        $message = $this->update->message;
        if (count(explode(' ', $message->text)) > 1) {
            $delivery = \Auth::user()->getDelivery();

            $delivery['fio'] = $message->text;
            \Auth::user()->setDelivery($delivery);

            \Telegram::deleteMessage([
                'chat_id' => $message->chat->id,
                'message_id' => $this->params['prev_message']
            ]);

            $paymentTypes = [];

            if (\Auth::user()->getCartTotal() >= config('delivery.overhead_payment_min_amount')) {
                $paymentTypes[] = ['text' => "Накладений платіж", 'callback_data' => "query=get-number&payment=" . TelegramService::OVERHEAD_PAYMENT];
            }

            $paymentTypes[] = ['text' => "Я сплатив", 'callback_data' => "query=get-number&payment=" . TelegramService::PAID];

            \Telegram::sendMessage([
                "chat_id" => $message->chat->id,
                'parse_mode' => 'html',
                'text' => "<span style='color:#f34848'>Звернiть увагу!!! " . PHP_EOL . PHP_EOL . "
            • Все обладнання та добрива відправляються на повну оплату новою поштою" . PHP_EOL . "
              • Насіння, аксесуари, суперечки грибів - можна сплатити післяплатою (нова пошта), якщо сума замовлення від 400 до 2500 грн." . PHP_EOL . "
            Умови доставки замовлень з товарами для вирощування можуть відрізнятися. Наші менеджери обов'язково зв'яжуться з вами і
            повідомлять про кількість посилок і терміни їх доставки. </span>" . PHP_EOL . PHP_EOL .
                    "<span style='color: red'>Доставка добрив здійснюється тільки після повної предоплати! </span>" . PHP_EOL . PHP_EOL .
                    "<span style='color: #b52323'>Замовлення до 400 грн відправляються за повною передоплатою: </span>" . PHP_EOL . PHP_EOL .
                    "<span style='color: #952626'>Доставка насіння за кордон здійснюється тільки в стелс-упаковці </span>" . PHP_EOL . PHP_EOL .
                    "Сплатити замовлення можна за реквізитами:" . PHP_EOL . "Карта 4035200041448009 " . PHP_EOL .
                    "ФОП Горова Людмила" . PHP_EOL . PHP_EOL . "Після оплати надішліть скріншот чека нашому оператору:",
                'reply_markup' => Keyboard::make(['inline_keyboard' =>
                    [
                        [
                            ['text' => "Надіслати чек", 'url' => "http://t.me/ErrorsSeeds_Support_bot"],
                            ['text' => "Хочу консультацію", 'url' => "http://t.me/ErrorsSeeds_Support_bot"],
                        ],
                        $paymentTypes,
                        [['text' => "Назад", 'callback_data' => "query=select-fio"]]
                    ]
                ])
            ]);
        } else {
            \Telegram::deleteMessage(['chat_id' => $message->chat->id, 'message_id' => $message->messageId]);
            try {
                \Telegram::editMessageText([
                    'chat_id' => $message->chat->id,
                    'message_id' => $this->params['prev_message'],
                    'text' => 'Напишіть ім\'я та прізвище одержувача посилки ' . PHP_EOL . '<b>(минимум 2 слова через пробіл)</b>',
                    'parse_mode' => 'HTML'
                ]);
                TelegramService::setNextAction('fio', $this->params);

            } catch (\Exception $e) {
                TelegramService::setNextAction('fio', $this->params);
            }
        }

    }

    public function total()
    {
        $message = $this->update->message;

        if ($message->text === TelegramService::CANCEL) {
            return \Telegram::sendMessage([
                'chat_id' => $message->chat->id,
                'text' => 'Ок',
                'reply_markup' => Keyboards::mainMenuKeyboard()
            ]);
        }
        if ($message->contact) {
            $contact = $message->contact->phoneNumber;
        } else {
            $contact = $message->text;
        }
        if (!preg_match('/^380\d\d\d\d\d\d\d\d\d/', $contact)) {
            try {
                \Telegram::deleteMessage(['chat_id' => $message->chat->id, 'message_id' => $message->messageId]);
                \Telegram::deleteMessage(['chat_id' => $message->chat->id, 'message_id' => $this->params['prev_message']]);
                $m = \Telegram::sendMessage([
                    'chat_id' => $message->chat->id,
                    'text' => '<b>номером повинен бути у форматі 380xxxxxxxxx. Без дефісів та пробів</b>',
                    'parse_mode' => 'HTML',
                ]);
                $this->params['prev_message'] = $m->messageId;
                TelegramService::setNextAction('total', $this->params);
            } catch (\Exception $e) {
                TelegramService::setNextAction('total', $this->params);
            }
        } else {
            $delivery = \Auth::user()->getDelivery();
            $delivery['phone'] = $contact;
            \Auth::user()->setDelivery($delivery);

            $text = TelegramService::cartText();
            $post = config('delivery.post.' . $delivery['post']);
            $postType = config('delivery.post-type.' . $delivery['post']);
            $paymentType = config('delivery.payment-type.' . $delivery['post']);
            $paymentTitle = config('delivery.post-type.' . $delivery['payment']);

            $text .= PHP_EOL . PHP_EOL;
            $text .= "<b>Адреса доставки:</b> {$delivery['city']}" . PHP_EOL;
            $text .= "<b>Метод доставки:</b> $post" . PHP_EOL;
            $text .= "<b>$postType:</b> {$delivery['postData']}" . PHP_EOL;
            $text .= "<b>ФИО Отримувача:</b> {$delivery['fio']}" . PHP_EOL;
            $text .= "<b>$paymentType:</b> $paymentTitle" . PHP_EOL;
            $text .= "<b>Запит номера:</b> {$delivery['phone']}" . PHP_EOL;

            \Telegram::deleteMessage(['chat_id' => $message->chat->id, 'message_id' => $this->params['prev_message']]);
            $m = \Telegram::sendMessage([
                'chat_id' => $message->chat->id,
                'text' => 'Кошик',
                'reply_markup' => Keyboards::mainMenuKeyboard()
            ]);
            \Telegram::deleteMessage(['chat_id' => $message->chat->id, 'message_id' => $m->messageId]);
            \Telegram::sendMessage([
                'chat_id' => $message->chat->id,
                'text' => $text,
                'parse_mode' => 'html',
                'reply_markup' => Keyboard::make([
                    'inline_keyboard' => [
                        [
                            ['text' => "Так", 'callback_data' => "query=confirm-order&confirm=" . 1],
                            ['text' => "Ні, отмєна", 'callback_data' => "query=confirm-order&confirm=" . 0],
                        ],
                        [['text' => "Назад", 'callback_data' => "query=repeat-number"]
                        ]
                    ],
                ])
            ]);
        }
    }
}
