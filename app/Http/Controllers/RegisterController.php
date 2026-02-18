<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class RegisterController extends Controller
{
    public function create()
    {
        return view('session.register');
    }

    public function store()
    {   
        $codigo = Str::random(9);
    
        $attributes = request()->validate([
            'name' => ['required', 'max:50'],
            'email' => ['required', 'email', 'max:50', Rule::unique('users', 'email')],
            'password' => ['required', 'min:5', 'max:20'],
            'agreement' => ['accepted'],
            'roles' => ['required', 'array'], // Valida que pelo menos uma função foi selecionada
            'roles.*' => ['in:dono,usuario,funcionario,contador,fornecedor,prestador'], // Valida cada função
        ]);
    
        $attributes['password'] = bcrypt($attributes['password']);
        $attributes['codigo'] = $codigo;
    
        // Cria o usuário
        $user = User::create($attributes);
    
        // Atribui as funções selecionadas
        if (!empty($attributes['roles'])) {
            $user->assignRole($attributes['roles']);
        }
    
        // Mensagem de sucesso e login
        session()->flash('success', 'Sua conta foi criada com sucesso.');
        Auth::login($user); 
        return redirect('/dashboard');
    }
}
