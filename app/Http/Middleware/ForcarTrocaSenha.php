<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ForcarTrocaSenha
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && Auth::user()->precisa_trocar_senha) {
            if (
                !$request->routeIs('primeiro-acesso.form') &&
                !$request->routeIs('primeiro-acesso.store') &&
                !$request->routeIs('logout') &&
                !$request->routeIs('politica.privacidade')
            ) {
                return redirect()->route('primeiro-acesso.form')
                                 ->with('info', 'Este é seu primeiro acesso. Por favor, crie uma nova senha e aceite os termos para continuar.');
            }
        }
        return $next($request);
    }
}