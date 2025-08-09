<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;

class TelegramService
{
    protected $token;
    protected $chatId;

    public function __construct()
    {
        $this->token = config('services.telegram.bot_token');
        $this->chatId = config('services.telegram.chat_id');
    }

    public function sendMessage($message)
    {
        $url = "https://api.telegram.org/bot{$this->token}/sendMessage";

        return Http::post($url, [
            'chat_id' => $this->chatId,
            'text' => $message,
        ]);
    }
}
