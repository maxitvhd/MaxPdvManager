<?php

namespace App\Http\Controllers;

use App\Models\SistemaPlano;
use Illuminate\Http\Request;

class SistemaPlanoController extends Controller
{
    public function index()
    {
        $planos = SistemaPlano::all();
        return view('admin.planos.index', compact('planos'));
    }

    public function create()
    {
        return view('admin.planos.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nome' => 'required|string|max:255',
            'meses_validade' => 'required|integer|min:1',
            'limite_dispositivos' => 'required|integer|min:1',
            'valor' => 'required|numeric|min:0',
        ]);

        SistemaPlano::create($data);
        return redirect()->route('planos.index')->with('success', 'Plano criado com sucesso.');
    }

    public function show(SistemaPlano $plano)
    {
        // View show not used by default
        return back();
    }

    public function edit(SistemaPlano $plano)
    {
        return view('admin.planos.edit', compact('plano'));
    }

    public function update(Request $request, SistemaPlano $plano)
    {
        $data = $request->validate([
            'nome' => 'required|string|max:255',
            'meses_validade' => 'required|integer|min:1',
            'limite_dispositivos' => 'required|integer|min:1',
            'valor' => 'required|numeric|min:0',
        ]);

        $plano->update($data);
        return redirect()->route('planos.index')->with('success', 'Plano atualizado com sucesso.');
    }

    public function destroy(SistemaPlano $plano)
    {
        $plano->delete();
        return redirect()->route('planos.index')->with('success', 'Plano removido.');
    }
}
