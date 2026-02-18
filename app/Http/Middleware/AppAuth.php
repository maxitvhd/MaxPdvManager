<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AppAuth
{
    public function handle(Request $request, Closure $next)
    {
        // NÃO força login
        // Apenas permite seguir o fluxo normalmente
        return $next($request);
    }
}
