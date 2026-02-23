<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function home()
    {
        // Se já estiver logado, manda pro dashboard
        if (auth()->check()) {
            return redirect()->route('dashboard');
        }

        // Busca os planos disponíveis
        $planos = \App\Models\SistemaPlano::all();

        // Busca os adicionais ativos
        $adicionais = \App\Models\SistemaAdicional::where('status', 'ativo')->get();

        return view('welcome', compact('planos', 'adicionais'));
    }
}
