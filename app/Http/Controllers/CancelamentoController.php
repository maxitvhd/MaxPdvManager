<?php
namespace App\Http\Controllers;

use App\Models\Loja;
use App\Models\LojaCancelamento;
use App\Models\LojaCancelamentoKey;
use App\Models\User;
use App\Models\LojaPermissao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CancelamentoController extends Controller
{
    public function index($lojaId)
    {
        $loja = Loja::findOrFail($lojaId);
        $this->verificarDono($loja);
        $cancelamentos = LojaCancelamento::where('loja_id', $lojaId)->with('user', 'cancelamentoKey.user')->get();
        return view('lojas.cancelamento.index', compact('loja', 'cancelamentos'));
    }

    public function chaves($lojaId)
    {
        $loja = Loja::findOrFail($lojaId);
        $this->verificarDono($loja);
        $chaves = LojaCancelamentoKey::where('loja_id', $lojaId)->with('user')->get();
        return view('lojas.cancelamento.chaves', compact('loja', 'chaves'));
    }

    public function createChave($lojaId)
{
    $loja = Loja::findOrFail($lojaId);
    $this->verificarDono($loja);

    // Busca os funcionários da loja (role = 'funcionario')
    $funcionarios = LojaPermissao::where('loja_id', $lojaId)
        ->where('role', 'funcionario')
        ->with('user') // Carrega os dados do usuário
        ->get()
        ->pluck('user'); // Extrai apenas os usuários

    return view('lojas.cancelamento.create_chave', compact('loja', 'funcionarios'));
}

public function storeChave(Request $request, $lojaId)
{
    $loja = Loja::findOrFail($lojaId);
    $this->verificarDono($loja);

    $request->validate([
        'user_id' => 'required|exists:users,id|exists:loja_permissao,user_id,loja_id,' . $lojaId . ',role,funcionario',
    ]);

    LojaCancelamentoKey::create([
        'loja_id' => $loja->id,
        'user_id' => $request->user_id,
    ]);

    return redirect()->route('lojas.cancelamento.chaves', $lojaId)->with('success', 'Chave criada!');
}

public function editChave($lojaId, $chaveId)
{
    $loja = Loja::findOrFail($lojaId);
    $this->verificarDono($loja);
    $chave = LojaCancelamentoKey::findOrFail($chaveId);

    // Busca os funcionários da loja (role = 'funcionario')
    $funcionarios = LojaPermissao::where('loja_id', $lojaId)
        ->where('role', 'funcionario')
        ->with('user')
        ->get()
        ->pluck('user');

    return view('lojas.cancelamento.edit_chave', compact('loja', 'chave', 'funcionarios'));
}

public function updateChave(Request $request, $lojaId, $chaveId)
{
    $loja = Loja::findOrFail($lojaId);
    $this->verificarDono($loja);
    $chave = LojaCancelamentoKey::findOrFail($chaveId);

    $request->validate([
        'user_id' => 'required|exists:users,id|exists:loja_permissao,user_id,loja_id,' . $lojaId . ',role,funcionario',
    ]);

    $chave->update(['user_id' => $request->user_id]);

    return redirect()->route('lojas.cancelamento.chaves', $lojaId)->with('success', 'Funcionário da chave atualizado!');
}

    public function destroyChave($lojaId, $chaveId)
    {
        $loja = Loja::findOrFail($lojaId);
        $this->verificarDono($loja);
        $chave = LojaCancelamentoKey::findOrFail($chaveId);
        $chave->delete();

        return redirect()->route('lojas.cancelamento.chaves', $lojaId)->with('success', 'Chave excluída!');
    }

    private function verificarDono($loja)
    {
        $user = Auth::user();
        if ($loja->user_id !== $user->id && !$loja->permissoes()->where('user_id', $user->id)->where('role', 'dono')->exists()) {
            abort(403, 'Apenas o dono pode realizar esta ação.');
        }
    }
}