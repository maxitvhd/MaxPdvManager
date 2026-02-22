<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramPostService
{
    /**
     * Envia uma foto com legenda para um chat (grupo ou canal) do Telegram.
     */
    public function postToChat($chatId, $botToken, $imagePath, $message)
    {
        try {
            $url = "https://api.telegram.org/bot{$botToken}/sendPhoto";

            $response = Http::attach(
                'photo',
                file_get_contents($imagePath),
                basename($imagePath)
            )->post($url, [
                        'chat_id' => $chatId,
                        'caption' => $message,
                        'parse_mode' => 'HTML'
                    ]);

            $result = $response->json();

            if (!$response->successful()) {
                Log::error("Erro Telegram Post: " . json_encode($result));
            }

            return $result;
        } catch (\Exception $e) {
            Log::error("Exceção Telegram Post: " . $e->getMessage());
            return ['ok' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Testa a conexão do bot e obtém informações básicas.
     */
    public function getMe($botToken)
    {
        try {
            $response = Http::get("https://api.telegram.org/bot{$botToken}/getMe");
            return $response->json();
        } catch (\Exception $e) {
            return ['ok' => false, 'error' => $e->getMessage()];
        }
    }
}
