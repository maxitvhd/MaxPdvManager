<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        'api/licenca',
        'api/dados',
        'api/produtos',
        'api/cancelamento/',
        'api/funcionarios',
        'api/clientes',
        'api/credito/sincronizar',
        '/api/dashboard/sincronizar',
        'api/store-produtos-unified',
        'api/upload-imagens-zip',
        'api/download-zip/*', // Adicionada para permitir o download do ZIP
        'api/mensagens',
        'app/login',
    ];
}
