<?php

namespace App\Services;

use App\Models\MaxDivulgaCampaign;
use App\Models\SocialAccount;
use Illuminate\Support\Facades\Log;

class SocialPublishService
{
    /**
     * Publica uma campanha em todos os canais selecionados nela
     * 
     * @param MaxDivulgaCampaign $campaign
     * @return array
     */
    public function publishToAll(MaxDivulgaCampaign $campaign)
    {
        $channels = $campaign->channels ?? [];
        if (empty($channels)) {
            return ['ok' => true, 'message' => 'Nenhum canal selecionado.'];
        }

        $lojaId = $campaign->loja_id;
        if (!$lojaId) {
            return ['ok' => false, 'message' => 'Campanha sem Loja ID vinculada.'];
        }

        // Caminhos de arquivo
        $imagePath = !empty($campaign->file_path) ? storage_path('app/public/' . str_replace('storage/', '', $campaign->file_path)) : null;
        $audioPath = !empty($campaign->audio_file_path) ? storage_path('app/public/' . str_replace('storage/', '', $campaign->audio_file_path)) : null;
        $message = $campaign->copy_acompanhamento;

        $results = [];

        foreach ($channels as $channel) {
            $channel = strtolower($channel);
            try {
                if ($channel === 'telegram') {
                    $results['telegram'] = $this->publishToTelegram($lojaId, $imagePath, $audioPath, $message);
                } elseif ($channel === 'facebook') {
                    $results['facebook'] = $this->publishToFacebook($lojaId, $imagePath, $message);
                }
            } catch (\Exception $e) {
                Log::error("[SOCIAL-PUBLISH] Erro crítico no canal {$channel}: " . $e->getMessage());
                $results[$channel] = ['ok' => false, 'error' => $e->getMessage()];
            }
        }

        return $results;
    }

    /**
     * Publica no Telegram para todas as contas conectadas da loja
     */
    private function publishToTelegram($lojaId, $imagePath, $audioPath, $message)
    {
        $accounts = SocialAccount::where('loja_id', $lojaId)->where('provider', 'telegram')->get();
        if ($accounts->isEmpty()) {
            return ['ok' => false, 'error' => 'Nenhuma conta Telegram conectada.'];
        }

        $service = new TelegramPostService();
        $count = 0;

        foreach ($accounts as $account) {
            if (!$imagePath || !file_exists($imagePath)) {
                Log::error("[SOCIAL-PUBLISH] Telegram falhou: Imagem não encontrada em {$imagePath}");
                continue;
            }

            // Envia Imagem
            $res = $service->postToChat($account->provider_id, $account->token, $imagePath, $message);

            if (isset($res['ok']) && $res['ok']) {
                $count++;
                // Envia áudio se houver
                if ($audioPath && file_exists($audioPath)) {
                    $service->postAudioToChat($account->provider_id, $account->token, $audioPath, '');
                }
            } else {
                Log::error("[SOCIAL-PUBLISH] Falha no Telegram Chat {$account->provider_id}: " . ($res['description'] ?? 'Erro desconhecido'));
            }
        }

        return ['ok' => $count > 0, 'published_count' => $count];
    }

    /**
     * Publica no Facebook (Páginas) para a última conta conectada (mecanismo básico)
     */
    private function publishToFacebook($lojaId, $imagePath, $message)
    {
        $account = SocialAccount::where('loja_id', $lojaId)->where('provider', 'facebook')->latest()->first();
        if (!$account) {
            return ['ok' => false, 'error' => 'Nenhuma conta Facebook conectada.'];
        }

        if (!$imagePath || !file_exists($imagePath)) {
            return ['ok' => false, 'error' => 'Imagem não encontrada.'];
        }

        $service = new FacebookPostService();

        // Se a conta tem páginas vinculadas no meta_data, postamos na primeira (ou iteramos)
        $pages = $account->meta_data['pages'] ?? [];
        if (!empty($pages)) {
            foreach ($pages as $page) {
                $pageId = $page['id'];
                $token = $page['access_token'] ?? $account->token;
                $service->postToPage($pageId, $token, $imagePath, $message);
            }
            return ['ok' => true, 'message' => 'Postado em ' . count($pages) . ' páginas.'];
        }

        // Tenta postar como grupo se não houver páginas (fallback legado)
        $service->postToGroup($account->provider_id, $account->token, $imagePath, $message);

        return ['ok' => true];
    }
}
