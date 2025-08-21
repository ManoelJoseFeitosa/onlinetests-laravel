<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PrimeiroAcessoController extends Controller
{
    public function create()
    {
        return view('auth.primeiro-acesso-senha');
    }

    public function store(Request $request)
    {
        $request->validate([
            'password' => ['required', 'confirmed', Password::min(8)],
            'aceite_termos' => 'required',
        ], [
            'aceite_termos.required' => 'Você deve ler e aceitar os Termos de Uso para continuar.',
        ]);

        $user = Auth::user();
        $user->password = Hash::make($request->password);
        $user->precisa_trocar_senha = false;
        $user->data_aceite_termos = now();
        $user->save();
        
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        Auth::login($user);

        return redirect()->intended(route('dashboard'))
                         ->with('success', 'Senha atualizada com sucesso! Bem-vindo(a)!');
    }
}