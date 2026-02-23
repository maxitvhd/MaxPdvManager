<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MaxDivulgaAdminController extends Controller
{
    public function index()
    {
        $config = \App\Models\MaxDivulgaConfig::first();
        return view('admin.maxdivulga.index', compact('config'));
    }

    public function storeConfig(Request $request)
    {
        $data = $request->validate([
            'provider_ia' => 'required|string',
            'api_key_ia' => 'nullable|string',
            'model_ia' => 'nullable|string',
            'provider_tts' => 'nullable|string',
            'tts_host' => 'nullable|string',
            'tts_api_key' => 'nullable|string',
            'tts_model' => 'nullable|string',
            'tts_voice' => 'nullable|string',
            'tts_default_speed' => 'nullable|numeric',
            'tts_default_noise_scale' => 'nullable|numeric',
            'tts_default_noise_w' => 'nullable|numeric',
            'facebook_client_id' => 'nullable|string',
            'facebook_client_secret' => 'nullable|string',
            'google_client_id' => 'nullable|string',
            'google_client_secret' => 'nullable|string',
        ]);

        $config = \App\Models\MaxDivulgaConfig::first();
        if ($config) {
            $config->update($data);
        } else {
            \App\Models\MaxDivulgaConfig::create($data);
        }

        return redirect()->route('admin.maxdivulga.index')->with('success', 'Configurações de IA salvas com sucesso.');
    }

    public function themes()
    {
        $themes = \App\Models\MaxDivulgaTheme::all();
        return view('admin.maxdivulga.themes', compact('themes'));
    }

    public function storeTheme(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'identifier' => 'required|string|unique:max_divulga_themes,identifier',
        ]);

        \App\Models\MaxDivulgaTheme::create([
            'name' => $request->name,
            'identifier' => $request->identifier,
            'path' => 'maxdivulga.themes.' . $request->identifier,
            'is_active' => true,
        ]);

        return redirect()->route('admin.maxdivulga.themes')->with('success', 'Tema registrado.');
    }

    // ─── AI Theme Generator ────────────────────────────────────────────

    public function themeCreateAi()
    {
        return view('admin.maxdivulga.theme_ai_generator');
    }

    /**
     * Recebe a configuraçao, monta o prompt e chama a IA para gerar o código Blade.
     * Retorna JSON { code: "...", error: null }
     */
    public function themeGenerateAi(\Illuminate\Http\Request $request)
    {
        $config = [
            'descricao' => $request->input('descricao', ''),
            'cor_primaria' => $request->input('cor_primaria', '#003A7A'),
            'cor_secundaria' => $request->input('cor_secundaria', '#FFD700'),
            'colunas' => intval($request->input('colunas', 3)),
            'estilo_card' => $request->input('estilo_card', 'detalhado'),
            'mostrar_desconto' => $request->boolean('mostrar_desconto', true),
            'area_ia_copy' => $request->boolean('area_ia_copy', true),
            'altura' => $request->input('altura', '1080x1920'),
        ];

        $svc = new \App\Services\AiCopyWriterService();
        $code = $svc->generateThemeCode($config);

        if (empty(trim($code))) {
            return response()->json(['code' => null, 'error' => 'A IA não retornou código. Verifique a conexão e a API Key.'], 422);
        }

        // Remove possíveis blocos markdown da IA
        $code = preg_replace('/^```(?:blade|html|php)?\s*/m', '', $code);
        $code = preg_replace('/^```\s*$/m', '', $code);
        $code = trim($code);

        return response()->json(['code' => $code, 'error' => null]);
    }

    /**
     * Salva o código Blade gerado como novo arquivo de tema.
     */
    public function themeSaveAi(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'identifier' => 'required|string|regex:/^[a-z0-9_]+$/|unique:max_divulga_themes,identifier',
            'code' => 'required|string',
        ]);

        $identifier = $request->identifier;
        $filePath = resource_path("views/maxdivulga/themes/{$identifier}.blade.php");

        // Garante o diretório
        \Illuminate\Support\Facades\File::ensureDirectoryExists(dirname($filePath));

        // Salva o arquivo
        file_put_contents($filePath, $request->code);

        // Registra no banco
        $theme = \App\Models\MaxDivulgaTheme::create([
            'name' => $request->name,
            'identifier' => $identifier,
            'path' => "maxdivulga.themes.{$identifier}",
            'is_active' => true,
        ]);

        // Limpa cache de views
        try {
            \Artisan::call('view:clear');
        } catch (\Throwable $e) {
        }

        return response()->json([
            'success' => true,
            'message' => "Tema \"{$request->name}\" salvo com sucesso!",
            'theme_id' => $theme->id,
            'theme_url' => route('admin.maxdivulga.themes'),
        ]);
    }
}

