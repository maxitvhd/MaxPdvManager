<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BancoAtivacaoController extends Controller
{
    /**
     * Exibe o formulário de ativação (facial + PIN)
     */
    public function form(string $token)
    {
        $cliente = Cliente::where('link_ativacao', $token)->first();

        if (!$cliente) {
            return view('bank.portal.ativacao', ['erro' => 'Link de ativação inválido ou não encontrado.']);
        }

        if (!$cliente->linkAtivacaoValido()) {
            return view('bank.portal.ativacao', [
                'erro' => 'Este link expirou. Solicite um novo link à sua loja.',
                'lojeNome' => optional($cliente->loja)->nome,
            ]);
        }

        return view('bank.portal.ativacao', compact('cliente'));
    }

    /**
     * Salva o facial_vector e PIN definidos pelo cliente
     */
    public function salvar(Request $request, string $token)
    {
        $cliente = Cliente::where('link_ativacao', $token)->first();

        if (!$cliente || !$cliente->linkAtivacaoValido()) {
            return redirect()->back()->with('error', 'Link inválido ou expirado.');
        }

        $request->validate([
            'pin'             => 'required|string|min:4|max:8|confirmed',
            'pin_confirmation'=> 'required|string',
            'facial_vector'   => 'nullable|string', // JSON enviado pelo face-api.js
            'foto_facial'     => 'nullable|image|max:5120',
        ], [
            'pin.required'    => 'Defina um PIN de 4 a 8 dígitos.',
            'pin.min'         => 'O PIN deve ter pelo menos 4 dígitos.',
            'pin.confirmed'   => 'Os PINs não coincidem.',
        ]);

        $dados = [
            'pin'           => $request->pin,
            'status'        => 'esp_documentos',  // próximo passo
            'link_ativacao' => null,               // invalida o link após uso
            'link_expires_at' => null,
        ];

        // Salva o vetor facial se enviado
        if ($request->filled('facial_vector')) {
            $dados['facial_vector'] = $request->facial_vector;
        }

        // Salva a foto facial no storage
        if ($request->hasFile('foto_facial')) {
            $loja    = $cliente->loja;
            $basePath = "lojas/{$loja->codigo}/clientes/{$cliente->codigo}";
            $arquivo = $request->file('foto_facial');
            $path    = "{$basePath}/facial.jpg";
            Storage::disk('local')->put($path, file_get_contents($arquivo->getRealPath()));
            $dados['foto_perfil'] = $path; // usa como foto de perfil inicial também
        }

        $cliente->update($dados);

        return view('bank.portal.ativacao', [
            'sucesso' => true,
            'cliente' => $cliente,
            'loginUrl' => route('banco.login'),
        ]);
    }
}
