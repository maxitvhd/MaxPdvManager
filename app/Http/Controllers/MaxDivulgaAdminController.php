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
}
