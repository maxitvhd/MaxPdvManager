<?php

namespace App\Traits;

use App\Models\Loja;
use Illuminate\Support\Facades\Auth;

trait ResolvesLoja
{
    /**
     * Detecta a loja do usuário logado.
     * Admin pode ver todas ou a loja passada por parâmetro.
     */
    protected function resolverLoja(?int $lojaId = null): ?Loja
    {
        $user = Auth::user();

        if (!$user) {
            return null;
        }

        // Admin / Super-Admin: usa qualquer loja
        if ($user->hasRole('admin') || $user->hasRole('super-admin')) {
            if ($lojaId) {
                return Loja::find($lojaId);
            }
            // Se não especificou, retorna a primeira disponível
            return Loja::first();
        }

        // Lojista normal: tenta pela sessão e depois pela FK user_id
        $lojaCodigo = session('loja_codigo');
        if ($lojaCodigo) {
            $loja = Loja::where('codigo', $lojaCodigo)->first();
            if ($loja) {
                return $loja;
            }
        }

        return Loja::where('user_id', $user->id)->first();
    }
}
