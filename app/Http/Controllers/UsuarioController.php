<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UsuarioController extends Controller
{
    public function index()
    {
        $usuarios = User::latest()->get();
        return view('usuarios.index', compact('usuarios'));
    }

    public function edit(User $usuario)
    {
        return view('usuarios.edit', compact('usuario'));
    }

    public function update(Request $request, User $usuario)
    {
        $attributes = $request->validate([
            'name' => ['required', 'max:50'],
            'email' => ['required', 'email', 'max:50', Rule::unique('users')->ignore($usuario->id)],
            'phone' => ['nullable', 'max:50'],
            'funcao' => ['nullable', 'string', 'max:50'],
            'acesso' => ['nullable', 'string', 'max:50'],
        ]);

        if ($request->filled('password')) {
            $request->validate([
                'password' => ['min:5', 'max:20']
            ]);
            $attributes['password'] = Hash::make($request->password);
        }

        $usuario->update($attributes);
        return redirect()->route('usuarios.index')->with('success', 'Usuário atualizado com sucesso!');
    }

    public function destroy(User $usuario)
    {
        if (auth()->id() === $usuario->id) {
            return back()->with('error', 'Você não pode excluir seu próprio usuário.');
        }

        $usuario->delete();
        return back()->with('success', 'Usuário excluído com sucesso.');
    }
}
