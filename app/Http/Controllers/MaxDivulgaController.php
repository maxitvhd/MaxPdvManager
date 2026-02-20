<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MaxDivulgaController extends Controller
{
    public function index()
    {
        $campaigns = \App\Models\MaxDivulgaCampaign::orderBy('created_at', 'desc')->get();
        return view('lojista.maxdivulga.index', compact('campaigns'));
    }

    public function create()
    {
        $themes = \App\Models\MaxDivulgaTheme::where('is_active', true)->get();
        return view('lojista.maxdivulga.create', compact('themes'));
    }

    public function store(Request $request)
    {
        // Lógica de salvamento e integração com IA ocorrerá através de Service (AiCopyWriterService)
        // Por ora, salvamos o modelo base.

        $campaign = \App\Models\MaxDivulgaCampaign::create([
            'tenant_id' => auth()->id() ?? null, // Exemplo
            'name' => $request->name ?? 'Campanha ' . now()->format('d/m/Y'),
            'type' => $request->type,
            'channels' => $request->channels,
            'schedule_type' => $request->schedule_type ?? 'unique',
            'product_selection_rule' => $request->product_selection_rule,
            'discount_rules' => $request->discount_rules,
            'theme_id' => $request->theme_id,
            'persona' => $request->persona,
            'format' => $request->format,
        ]);

        // Dispararia os Services aqui...

        return redirect()->route('lojista.maxdivulga.index')->with('success', 'Campanha criada e gerada com sucesso!');
    }

    public function download(\App\Models\MaxDivulgaCampaign $campaign)
    {
        // Retorna o arquivo gerado (imagem, pdf ou audio)
        return response()->json(['message' => 'Download ' . $campaign->id]);
    }

    public function apiProducts(Request $request)
    {
        // Retorna lista de produtos do lojista para o Vue/Alpine preencher
        // $produtos = Produto::all();
        return response()->json([]);
    }
}
