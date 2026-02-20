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

        $rules = [
            'loja_id' => 'required',
            'descricao' => 'required',
        ];

        if (Auth::user()->hasRole('admin')) {
            $rules['validade'] = 'required|date';
        }

        $request->validate($rules);

        $data = $request->all();
        $data['user_id'] = Auth::id();
        $data['codigo'] = $codigo;

        if (!Auth::user()->hasRole('admin')) {
            unset($data['validade']);
            $data['status'] = 'inativo'; // Requererá compra de plano
        }

        Licenca::create($data);

        return redirect()->route('licencas.index')->with('success', 'Licença criada com sucesso!');
    }

    public function edit($id)
    {
        $licenca = Licenca::findOrFail($id);
        $lojas = Loja::all();
        return view('licencas.edit', compact('licenca', 'lojas'));
    }

    public function update(Request $request, $id)
    {
        $rules = [
            'loja_id' => 'required',
            'descricao' => 'required'
        ];

        // Bloqueia update de validade por lojistas
        if (Auth::user()->hasRole('admin')) {
            $rules['validade'] = 'required|date';
        }

        $request->validate($rules);
        $licenca = Licenca::findOrFail($id);

        $data = $request->all();
        if (!Auth::user()->hasRole('admin')) {
            unset($data['validade']);
        }

        $licenca->update($data);

        return redirect()->route('licencas.index')->with('success', 'Licença atualizada com sucesso!');
    }

    public function destroy($id)
    {
        $licenca = Licenca::findOrFail($id);
        $licenca->delete();

        return redirect()->route('licencas.index')->with('success', 'Licença excluída com sucesso!');
    }
}
