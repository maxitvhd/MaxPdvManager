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

        $request->validate([
            'loja_id' => 'required',
            'codigo' =>  $codigo,
            'descricao' => 'required',
            'validade' => 'required|date',

        ]);
        $request['user_id'] = Auth::id();
        $request['codigo'] = $codigo;

        Licenca::create($request->all());

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
        $request->validate([
            'loja_id' => 'required',
            'descricao' => 'required',
            'validade' => 'required|date',
        ]);
       //dd($request);
        $licenca = Licenca::findOrFail($id);
        $licenca->update($request->all());

        return redirect()->route('licencas.index')->with('success', 'Licença atualizada com sucesso!');
    }

    public function destroy($id)
    {
        $licenca = Licenca::findOrFail($id);
        $licenca->delete();

        return redirect()->route('licencas.index')->with('success', 'Licença excluída com sucesso!');
    }
}
