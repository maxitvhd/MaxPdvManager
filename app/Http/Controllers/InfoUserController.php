<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\View;

class InfoUserController extends Controller
{

    public function create()
    {
        return view('laravel-examples/user-profile');
    }

    public function profile()
    {
        return view('profile');
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $attributes = $request->validate([
            'name' => ['required', 'max:50'],
            'email' => ['required', 'email', 'max:50', Rule::unique('users')->ignore($user->id)],
            'phone' => ['nullable', 'max:50'],
            'endereco' => ['nullable', 'max:150'],
            'bairro' => ['nullable', 'max:50'],
            'cidade' => ['nullable', 'max:50'],
            'estado' => ['nullable', 'max:2'],
            'cep' => ['nullable', 'max:20'],
            'location' => ['nullable', 'max:70'],
            'geolocalizacao' => ['nullable', 'max:150'],
            'imagem' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
            // social networks
            'facebook' => ['nullable', 'url', 'max:150'],
            'twitter' => ['nullable', 'url', 'max:150'],
            'instagram' => ['nullable', 'url', 'max:150'],
            'linkedin' => ['nullable', 'url', 'max:150'],
            'about_text' => ['nullable', 'string', 'max:500'],
        ]);

        if (env('IS_DEMO') && $user->id == 1 && $request->get('email') != $user->email) {
            return redirect()->back()->withErrors(['msg2' => 'You are in a demo version, you can\'t change the email address.']);
        }

        // Handle Image Upload
        if ($request->hasFile('imagem')) {
            $imageName = time() . '.' . $request->imagem->extension();
            $request->imagem->move(public_path('assets/img/users'), $imageName);
            $user->imagem = 'assets/img/users/' . $imageName;
        }

        // Handle Social Links to about_me
        $socials = [
            'facebook' => $request->get('facebook'),
            'twitter' => $request->get('twitter'),
            'instagram' => $request->get('instagram'),
            'linkedin' => $request->get('linkedin'),
            'about_text' => $request->get('about_text'),
        ];
        
        $user->name = $attributes['name'];
        $user->email = $attributes['email'];
        if (isset($attributes['phone'])) $user->phone = $attributes['phone'];
        if (isset($attributes['endereco'])) $user->endereco = $attributes['endereco'];
        if (isset($attributes['bairro'])) $user->bairro = $attributes['bairro'];
        if (isset($attributes['cidade'])) $user->cidade = $attributes['cidade'];
        if (isset($attributes['estado'])) $user->estado = $attributes['estado'];
        if (isset($attributes['cep'])) $user->cep = $attributes['cep'];
        if (isset($attributes['location'])) $user->location = $attributes['location'];
        if ($request->filled('geolocalizacao')) $user->geolocalizacao = $attributes['geolocalizacao'];
        $user->ip = $request->ip();
        
        $user->about_me = json_encode($socials);
        
        $user->save();

        return redirect('/profile')->with('success', 'Perfil atualizado com sucesso!');
    }

    public function store(Request $request)
    {

        $attributes = request()->validate([
            'name' => ['required', 'max:50'],
            'email' => ['required', 'email', 'max:50', Rule::unique('users')->ignore(Auth::user()->id)],
            'phone'     => ['max:50'],
            'location' => ['max:70'],
            'about_me'    => ['max:150'],
        ]);
        if($request->get('email') != Auth::user()->email)
        {
            if(env('IS_DEMO') && Auth::user()->id == 1)
            {
                return redirect()->back()->withErrors(['msg2' => 'You are in a demo version, you can\'t change the email address.']);
                
            }
            
        }
        else{
            $attribute = request()->validate([
                'email' => ['required', 'email', 'max:50', Rule::unique('users')->ignore(Auth::user()->id)],
            ]);
        }
        
        
        User::where('id',Auth::user()->id)
        ->update([
            'name'    => $attributes['name'],
            'email' => $attribute['email'],
            'phone'     => $attributes['phone'],
            'location' => $attributes['location'],
            'about_me'    => $attributes["about_me"],
        ]);


        return redirect('/user-profile')->with('success','Profile updated successfully');
    }
}
