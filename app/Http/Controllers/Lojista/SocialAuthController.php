<?php

namespace App\Http\Controllers\Lojista;

use App\Http\Controllers\Controller;
use App\Models\SocialAccount;
use App\Models\MaxDivulgaConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Traits\ResolvesLoja;

class SocialAuthController extends Controller
{
    use ResolvesLoja;
    public function redirectToProvider($provider)
    {
        $config = MaxDivulgaConfig::first();

        if ($provider === 'facebook') {
            if (!$config || !$config->facebook_client_id) {
                return back()->with('error', 'Configurações do Facebook Apps não encontradas no Admin.');
            }

            $url = "https://www.facebook.com/v18.0/dialog/oauth?" . http_build_query([
                'client_id' => $config->facebook_client_id,
                'redirect_uri' => route('facebook.callback', 'facebook'),
                'scope' => 'pages_show_list,pages_read_engagement,pages_manage_posts,publish_video,groups_access_member_info,publish_to_groups',
                'response_type' => 'code',
                'state' => csrf_token(),
            ]);

            return redirect($url);
        }

        return back()->with('error', 'Provedor não suportado.');
    }

    public function handleProviderCallback(Request $request, $provider)
    {
        $config = MaxDivulgaConfig::first();
        $code = $request->code;

        if (!$code) {
            return redirect()->route('lojista.maxdivulga.canais.index')->with('error', 'Autorização cancelada.');
        }

        if ($provider === 'facebook') {
            // 1. Trocar code por User Access Token
            $response = Http::get("https://graph.facebook.com/v18.0/oauth/access_token", [
                'client_id' => $config->facebook_client_id,
                'client_secret' => $config->facebook_client_secret,
                'redirect_uri' => route('facebook.callback', 'facebook'),
                'code' => $code,
            ]);

            if ($response->failed()) {
                return redirect()->route('lojista.maxdivulga.canais.index')->with('error', 'Falha ao obter token do Facebook.');
            }

            $tokenData = $response->json();
            $userToken = $tokenData['access_token'];

            // 2. Obter dados do usuário (ID e Nome)
            $userResponse = Http::get("https://graph.facebook.com/me", [
                'access_token' => $userToken,
                'fields' => 'id,name'
            ]);

            $userData = $userResponse->json();

            // 3. Obter Páginas (opcional nesta etapa, mas útil para o meta_data)
            $pagesResponse = Http::get("https://graph.facebook.com/me/accounts", [
                'access_token' => $userToken
            ]);
            $pages = $pagesResponse->json()['data'] ?? [];

            // 4. Salvar ou Atualizar Conta Social
            $loja = $this->resolverLoja();
            SocialAccount::updateOrCreate(
                [
                    'loja_id' => $loja->id ?? null,
                    'provider' => 'facebook',
                ],
                [
                    'provider_id' => $userData['id'],
                    'token' => $userToken,
                    'meta_data' => [
                        'name' => $userData['name'],
                        'pages' => $pages,
                    ],
                    'expires_at' => isset($tokenData['expires_in']) ? now()->addSeconds($tokenData['expires_in']) : null,
                ]
            );

            return redirect()->route('lojista.maxdivulga.canais.index')->with('success', 'Facebook conectado com sucesso!');
        }

        return redirect()->route('lojista.maxdivulga.canais.index')->with('error', 'Provedor inválido.');
    }

    public function connectTelegram(Request $request)
    {
        $request->validate([
            'chat_id' => 'required|string',
            'chat_name' => 'required|string',
            'bot_token' => 'required|string',
        ]);

        $service = new \App\Services\TelegramPostService();

        // 1. Validar se o Bot Token existe
        $botInfo = $service->getMe($request->bot_token);
        if (!isset($botInfo['ok']) || !$botInfo['ok']) {
            return back()->with('error', 'Token do Bot inválido ou expirado. Verifique no @BotFather.');
        }

        // 2. Validar se o Bot tem acesso ao Chat ID
        $chatInfo = $service->getChat($request->chat_id, $request->bot_token);
        if (!isset($chatInfo['ok']) || !$chatInfo['ok']) {
            $errorMsg = $chatInfo['description'] ?? 'Chat não encontrado.';
            if ($errorMsg === 'Bad Request: chat not found') {
                $errorMsg = 'Canal/Grupo não encontrado. Verifique se o Bot é Administrador e se o ID está correto (ex: -100...)';
            }
            return back()->with('error', 'Erro no Telegram: ' . $errorMsg);
        }

        $loja = $this->resolverLoja();
        SocialAccount::updateOrCreate(
            [
                'loja_id' => $loja->id ?? null,
                'provider' => 'telegram',
                'provider_id' => trim($request->chat_id),
            ],
            [
                'token' => $request->bot_token,
                'meta_data' => [
                    'name' => $request->chat_name,
                    'type' => $chatInfo['result']['type'] ?? 'unknown',
                    'username' => $chatInfo['result']['username'] ?? null,
                ],
            ]
        );

        return redirect()->route('lojista.maxdivulga.canais.index')->with('success', 'Canal Telegram "' . $request->chat_name . '" conectado com sucesso!');
    }

    public function publish(Request $request, $campaignId)
    {
        $request->validate([
            'provider' => 'required|string',
            'target_id' => 'required|string', // ID da página ou grupo
            'target_type' => 'required|string|in:page,group,supergroup,channel',
        ]);

        $campaign = \App\Models\MaxDivulgaCampaign::findOrFail($campaignId);

        // Se a campanha for uma programação (parent) sem arquivos, busca a última instância gerada (filha) que tenha arquivos
        if (empty($campaign->file_path) && empty($campaign->audio_file_path)) {
            $latestChild = \App\Models\MaxDivulgaCampaign::where('parent_id', $campaign->id)
                ->whereNotNull('file_path')
                ->latest()
                ->first();

            if ($latestChild) {
                if (env('LOG_MAXDIVULGA', true)) {
                    \Illuminate\Support\Facades\Log::info("[SOCIAL-PUBLISH] Usando mídia da campanha filha #{$latestChild->id} para a mãe #{$campaign->id}");
                }
                $campaign = $latestChild;
            }
        }

        $service = new \App\Services\SocialPublishService();
        $results = $service->publishToAll($campaign);

        $provider = $request->provider;
        $res = $results[$provider] ?? ['ok' => false, 'error' => 'Falha ao processar provedor.'];

        if ($res['ok']) {
            $msg = ($provider === 'telegram' && isset($res['published_count']))
                ? "Publicado com sucesso no Telegram!"
                : "Publicado com sucesso no " . ucfirst($provider) . "!";
            return back()->with('success', $msg);
        }

        $errorMsg = $res['description'] ?? $res['error'] ?? 'Erro desconhecido.';
        if ($errorMsg === 'Bad Request: chat not found') {
            $errorMsg = 'Canal/Grupo não encontrado. Verifique se o Bot é Administrador no Telegram e se o Canal foi reconectado recentemente.';
        }

        return back()->with('error', 'Erro ao publicar no ' . ucfirst($provider) . ': ' . $errorMsg);
    }

    public function disconnect($provider)
    {
        $loja = $this->resolverLoja();
        SocialAccount::where('loja_id', $loja->id ?? null)
            ->where('provider', $provider)
            ->delete();

        return back()->with('success', ucfirst($provider) . ' desconectado.');
    }
}
