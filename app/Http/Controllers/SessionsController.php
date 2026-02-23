<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class SessionsController extends Controller
{
    public function create()
    {
        return view('session.login-session');
    }

    public function store()
    {
        $attributes = request()->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (Auth::attempt($attributes)) {
            session()->regenerate();
            return redirect('dashboard');
        } else {

            return back()->withErrors(['email' => 'Email ou senha invalida.']);
        }
    }

    // Dentro da classe SessionsController
    public function app(Request $request)
    {
        // 1. Validação
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        // 2. Tentativa de Login
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            // TRUQUE PARA O APP ORIGINAL:
            // Não retornamos redirect(), retornamos JSON 200 OK.
            // Isso faz o 'fetch' do React entender que foi sucesso.
            return response()->json([
                'success' => true,
                'message' => 'Login realizado com sucesso',
                'redirect' => route('app.pdv') // Sugere para onde ir
            ], 200);
        }

        // 3. Em caso de erro
        return response()->json([
            'success' => false,
            'message' => 'Email ou senha inválidos.'
        ], 401);
    }

    public function destroy()
    {

        Auth::logout();

        return redirect('/login')->with(['success' => 'Você\'acabou de sair com sucesso.']);
    }
}
