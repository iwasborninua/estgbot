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
        $delivery = \Auth::user()->getDelivery();

        $delivery['fio'] = $message->text;
        \Auth::user()->setDelivery($delivery);

        \Telegram::deleteMessage([
            'chat_id' => $message->chat->id,
            'message_id' => $this->params['prev_message']
        ]);

        \Telegram::sendMessage([
            "chat_id" => $message->chat->id,
            'text' => "Замовлення до 400 грн відправляються за повною передоплатою: " . PHP_EOL . PHP_EOL .
                "Сплатити замовлення можна за реквізитами:" . PHP_EOL . "Карта 4035200041448009 " . PHP_EOL .
                "ФОП Горова Людмила" . PHP_EOL . PHP_EOL . "Після оплати надішліть скріншот чека нашому оператору:",
            'reply_markup' => Keyboard::make(['inline_keyboard' =>
                [
                    [
                        ['text' => "Надіслати чек", 'url' => "http://t.me/ErrorsSeeds_Support_bot"],
                        ['text' => "Хочу консультацію", 'url' => "http://t.me/ErrorsSeeds_Support_bot"],
                    ],
                    [
                        ['text' => "Накладений платіж", 'callback_data' => "query=get-number&payment=" . TelegramService::OVERHEAD_PAYMENT],
                        ['text' => "Я сплатив", 'callback_data' => "query=get-number&payment=" . TelegramService::PAID],
                    ],
                    [['text' => "Назад", 'callback_data' => "query=select-fio"]]
                ]
            ])
        ]);
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
        if (!$contact) {
            \Telegram::deleteMessage(['chat_id' => $message->chat->id, 'message_id' => $message->messageId]);
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
