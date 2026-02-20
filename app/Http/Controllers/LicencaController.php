<?php
namespace App\Http\Controllers;

use App\Models\Licenca;
use App\Models\Checkout;

use App\Models\Loja;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class LicencaController extends Controller
{
    public function index()
    {
        $licencas = Licenca::with('loja', 'checkout')->get();
        return view('licencas.index', compact('licencas'));
    }

    public function create()
    {
        $lojas = Loja::all();
        return view('licencas.create', compact('lojas'));
    }

    public function store(Request $request)
    {
        $codigo = Str::random(30);
        $key = Str::random(40); // Gerar a chave secreta

        $rules = [
            'loja_id' => 'required',
            'descricao' => 'required',
        ];

        if (Auth::user()->hasRole('admin') || Auth::user()->hasRole('super-admin')) {
            $rules['validade'] = 'required|date';
        }

        $request->validate($rules);

        $data = $request->all();
        $data['user_id'] = Auth::id();
        $data['codigo'] = $codigo;
        $data['key'] = $key; // Salvar a chave

        if (!Auth::user()->hasRole('admin') && !Auth::user()->hasRole('super-admin')) {
            $data['validade'] = now()->toDateString(); // Default
            $data['status'] = 'inativo'; // Requererá compra de plano
        }

        Licenca::create($data);

        return redirect()->route('licencas.index')->with('success', 'Licença criada com sucesso!');
    }

    public function edit($codigo)
    {
        $licenca = Licenca::where('codigo', $codigo)->firstOrFail();

        // Verifica se a Key existe, caso seja antiga e nula, gera uma por segurança na hora
        if (!$licenca->key) {
            $licenca->key = Str::random(40);
            $licenca->save();
        }

        $lojas = Loja::all();
        return view('licencas.edit', compact('licenca', 'lojas'));
    }

    public function update(Request $request, $codigo)
    {
        $rules = [
            'loja_id' => 'required',
            'descricao' => 'required'
        ];

        // Bloqueia update de validade por lojistas
        if (Auth::user()->hasRole('admin') || Auth::user()->hasRole('super-admin')) {
            $rules['validade'] = 'required|date';
        }

        $request->validate($rules);
        $licenca = Licenca::where('codigo', $codigo)->firstOrFail();

        $data = $request->all();
        if (!Auth::user()->hasRole('admin') && !Auth::user()->hasRole('super-admin')) {
            unset($data['validade']);
            unset($data['status']);
        }

        $licenca->update($data);

        return redirect()->route('licencas.index')->with('success', 'Licença atualizada com sucesso!');
    }

    public function destroy($codigo)
    {
        $licenca = Licenca::where('codigo', $codigo)->firstOrFail();
        $licenca->delete();

        return redirect()->route('licencas.index')->with('success', 'Licença excluída com sucesso!');
    }
}
