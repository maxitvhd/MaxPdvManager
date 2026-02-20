<?php

namespace App\Http\Controllers;

use App\Models\SistemaAdicional;
use Illuminate\Http\Request;

class SistemaAdicionalController extends Controller
{
    public function __construct()
    {
        // Garante que apenas Admin pode gerenciar adicionais
        $this->middleware('role:admin|super-admin');
    }

    public function index()
    {
        $adicionais = SistemaAdicional::all();
        return view('admin.adicionais.index', compact('adicionais'));
    }

    public function create()
    {
        return view('admin.adicionais.create');
    }

    public function store(Request $request)
    {
        if ($request->has('valor')) {
            $request->merge(['valor' => str_replace(',', '.', $request->valor)]);
        }

        $data = $request->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'tipo' => 'required|in:dispositivo,modulo',
            'valor' => 'required|numeric|min:0',
        ]);

        $data['status'] = $request->has('status');

        SistemaAdicional::create($data);

        return redirect()->route('adicionais.index')->with('success', 'Adicional criado com sucesso.');
    }

    public function edit(SistemaAdicional $adicionai)
    {
        // O Laravel usa a variavel $adicionai por causa do plural/singular gerado
        $adicional = $adicionai;
        return view('admin.adicionais.edit', compact('adicional'));
    }

    public function update(Request $request, SistemaAdicional $adicionai)
    {
        if ($request->has('valor')) {
            $request->merge(['valor' => str_replace(',', '.', $request->valor)]);
        }

        $adicional = $adicionai;
        $data = $request->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'tipo' => 'required|in:dispositivo,modulo',
            'valor' => 'required|numeric|min:0',
        ]);

        $data['status'] = $request->has('status');

        $adicional->update($data);

        return redirect()->route('adicionais.index')->with('success', 'Adicional atualizado com sucesso.');
    }

    public function destroy(SistemaAdicional $adicionai)
    {
        $adicional = $adicionai;
        $adicional->delete();
        return redirect()->route('adicionais.index')->with('success', 'Adicional removido.');
    }
}
