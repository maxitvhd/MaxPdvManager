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
        $user = Auth::user();
        $query = Licenca::with('loja', 'checkout');

        if (!$user->hasRole('admin') && !$user->hasRole('super-admin')) {
            $query->where('user_id', $user->id);
        }

        $licencas = $query->get();
        return view('licencas.index', compact('licencas'));
    }

    public function create()
    {
        $user = Auth::user();
        if ($user->hasRole('admin') || $user->hasRole('super-admin')) {
            $lojas = Loja::all();
        } else {
            $lojas = Loja::where('user_id', $user->id)->get();
        }

        return view('licencas.create', compact('lojas'));
    }

    public function store(Request $request)
    {
        $codigo = strtoupper(Str::random(6));
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
        $user = Auth::user();
        $licenca = Licenca::where('codigo', $codigo)->firstOrFail();

        // Segurança: Lojista só vê/edita o que é dele
        if (!$user->hasRole('admin') && !$user->hasRole('super-admin')) {
            if ($licenca->user_id !== $user->id) {
                abort(403, 'Acesso não autorizado a esta licença.');
            }
            $lojas = Loja::where('user_id', $user->id)->get();
        } else {
            $lojas = Loja::all();
        }

        // Verifica se a Key existe, caso seja antiga e nula, gera uma por segurança na hora
        if (!$licenca->key) {
            $licenca->key = Str::random(40);
            $licenca->save();
        }

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

        // Se a licença, após a atualização, estiver vencida ou inativa, desativa todos os PDVs conectados a ela
        if (!$licenca->isValid()) {
            Checkout::where('licenca_id', $licenca->id)
                ->where('status', 'ativo')
                ->update(['status' => 'inativo']);
        }

        return redirect()->route('licencas.index')->with('success', 'Licença atualizada com sucesso!');
    }

    public function destroy($codigo)
    {
        $licenca = Licenca::where('codigo', $codigo)->firstOrFail();
        $licenca->delete();

        return redirect()->route('licencas.index')->with('success', 'Licença excluída com sucesso!');
    }
}
