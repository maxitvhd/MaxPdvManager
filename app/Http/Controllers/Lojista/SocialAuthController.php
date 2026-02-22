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
        ]);

        $loja = $this->resolverLoja();
        SocialAccount::updateOrCreate(
            [
                'loja_id' => $loja->id ?? null,
                'provider' => 'telegram',
                'provider_id' => $request->chat_id,
            ],
            [
                'token' => 'bot_token_used_globally', // Poderia ser customizado por loja se necessário
                'meta_data' => [
                    'name' => $request->chat_name,
                    'type' => str_starts_with($request->chat_id, '-') ? 'group/channel' : 'user',
                ],
            ]
        );

        return redirect()->route('lojista.maxdivulga.canais.index')->with('success', 'Canal Telegram registrado!');
    }

    public function publish(Request $request, $campaignId)
    {
        $request->validate([
            'provider' => 'required|string',
            'target_id' => 'required|string', // ID da página ou grupo
            'target_type' => 'required|string|in:page,group',
        ]);

        $campaign = \App\Models\MaxDivulgaCampaign::findOrFail($campaignId);
        $account = SocialAccount::where('loja_id', auth()->user()->loja_id)
            ->where('provider', $request->provider)
            ->firstOrFail();

        $service = new \App\Services\FacebookPostService();
        $imagePath = storage_path('app/public/' . $campaign->file_path);
        $message = $campaign->copy_social ?? $campaign->copy_acompanhamento;

        if ($request->provider === 'facebook') {
            if ($request->target_type === 'page') {
                // Para página, precisamos do Page Access Token se disponível no meta_data
                $page = collect($account->meta_data['pages'] ?? [])->where('id', $request->target_id)->first();
                $token = $page['access_token'] ?? $account->token;

                $result = $service->postToPage($request->target_id, $token, $imagePath, $message);
            } else {
                $result = $service->postToGroup($request->target_id, $account->token, $imagePath, $message);
            }

            if (isset($result['id']) || isset($result['post_id'])) {
                return back()->with('success', 'Publicado com sucesso no Facebook!');
            }

            return back()->with('error', 'Erro ao publicar: ' . ($result['error'] ?? 'Erro desconhecido.'));
        }

        if ($request->provider === 'telegram') {
            $config = MaxDivulgaConfig::first();
            if (!$config || !$config->telegram_bot_token) {
                return back()->with('error', 'Bot Token do Telegram não configurado no Admin.');
            }

            $service = new \App\Services\TelegramPostService();
            // Para o Telegram, o target_id é o Chat ID
            $result = $service->postToChat($request->target_id, $config->telegram_bot_token, $imagePath, $message);

            if (isset($result['ok']) && $result['ok']) {
                return back()->with('success', 'Publicado com sucesso no Telegram!');
            }

            return back()->with('error', 'Erro ao publicar no Telegram: ' . ($result['description'] ?? 'Erro desconhecido.'));
        }

        return back()->with('error', 'Provedor não suportado.');
    }

    public function disconnect($provider)
    {
        SocialAccount::where('loja_id', auth()->user()->loja_id)
            ->where('provider', $provider)
            ->delete();

        return back()->with('success', ucfirst($provider) . ' desconectado.');
    }
}
