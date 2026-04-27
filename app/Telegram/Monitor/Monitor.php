<?php

namespace App\Telegram\Monitor;

use Illuminate\Support\Facades\Http;
use Telegram\Bot\Laravel\Facades\Telegram;

class Monitor
{
    public  function checkBlogs()
    {
        $data = HTTP::get(config('constants.sklad_monitor_check_url'), ['keyword' => config('auth.sklad-monitor-keyword')])->json();
        $message = '';
        if ($data){
            foreach ($data as $res){{
                if ($res['http_code'] !== 200){
                    $message.= 'Проблема с ' . $res['url'] . ' статус ' . $res['http_code'] . ($res['message'] ? ". {$res['$message']}" : '') . PHP_EOL ;
                }
            }}
        }else{
            $message = 'Пустой ответ от сервера ' . config('constants.sklad_monitor_check_url');

        }

        if($message){
            $this->sendToChannel($message);
        }
    }

    private function sendToChannel($message): void
    {
        try {
            Telegram::bot('es_monitor_admin_bot')->sendMessage([
                'chat_id' => config('telegram.bots.es_monitor_admin_bot.monitor_tg_id'),
                'text' => $message,
                'parse_mode' => 'HTML'
            ]);
        }catch (\Exception $e) {
            \Log::error("Ошибка Отправки в канал MONITOR: " . $e->getMessage(), ['MONITOR']);
        }

    }
}
