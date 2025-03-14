<?php

return [
    'post' => [
        'novapost' => 'Нова пошта',
        'ukrpost' => 'Укрпошта',
        'ukrpost-kz' => 'Укрпошта (Казахстан)'
    ],
    'post-type' => [
        'novapost' => 'Відділення Нової Пошти',
        'ukrpost' => 'Индекс та адреса',
        'ukrpost-kz' => 'Индекс та адреса',
    ],
    'payment-type' =>[
        'novapost' => 'Оплата НП',
        'ukrpost' => 'Оплата Укрпошта',
        'ukrpost-kz' => 'Оплата Укрпошта'
    ],
    'payment' => [
        \App\Telegram\TelegramService::OVERHEAD_PAYMENT => 'Накладений платіж',
        \App\Telegram\TelegramService::PAID => 'Я сплатив'
    ]
];
