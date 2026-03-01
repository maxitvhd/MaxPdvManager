<?php

namespace App\Http\Middleware;

use App\Models\Cliente;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AutenticacaoCliente
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!session('cliente_codigo')) {
            return redirect()->route('banco.login')
                ->with('error', 'Faça login para acessar sua conta.');
        }

        // Revalida o cliente no banco (segurança)
        $cliente = Cliente::where('codigo', session('cliente_codigo'))->first();

        if (!$cliente || !in_array($cliente->status, ['ativo', 'pag_atrasado', 'cobranca'])) {
            session()->forget(['cliente_codigo', 'cliente_loja_id']);
            return redirect()->route('banco.login')
                ->with('error', 'Sua conta não está disponível para acesso.');
        }

        // Injeta o cliente na request para os controllers usarem
        $request->merge(['_cliente' => $cliente]);
        view()->share('clienteLogado', $cliente);

        return $next($request);
    }
}
