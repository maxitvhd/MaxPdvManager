<?php

namespace App\Services;

use App\Models\SocialAccount;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FacebookPostService
{
    /**
     * Posta uma imagem em uma Página do Facebook
     */
    public function postToPage($pageId, $pageAccessToken, $imagePath, $message)
    {
        try {
            // No Graph API, para postar imagem em página: /PAGE_ID/photos
            $response = Http::post("https://graph.facebook.com/v18.0/{$pageId}/photos", [
                'access_token' => $pageAccessToken,
                'url' => bin2hex(file_get_contents($imagePath)), // Se for URL pública funciona melhor, se for binário precisa de multipart
                'message' => $message,
            ]);

            // Como as imagens do sistema estão em paths locais, o ideal é usar multipart
            $response = Http::attach(
                'source',
                file_get_contents($imagePath),
                basename($imagePath)
            )->post("https://graph.facebook.com/v18.0/{$pageId}/photos", [
                        'access_token' => $pageAccessToken,
                        'message' => $message,
                    ]);

            return $response->json();
        } catch (\Exception $e) {
            Log::error("Erro Facebook Post Page: " . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Posta uma imagem em um Grupo do Facebook
     * Requer que o APP esteja instalado no grupo.
     */
    public function postToGroup($groupId, $userAccessToken, $imagePath, $message)
    {
        try {
            $response = Http::attach(
                'source',
                file_get_contents($imagePath),
                basename($imagePath)
            )->post("https://graph.facebook.com/v18.0/{$groupId}/photos", [
                        'access_token' => $userAccessToken,
                        'message' => $message,
                    ]);

            return $response->json();
        } catch (\Exception $e) {
            Log::error("Erro Facebook Post Group: " . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }
}
