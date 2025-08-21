<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User;
use App\Models\Questao;

class CheckPlanLimits
{
    public function handle(Request $request, Closure $next, string ...$recursos): Response
{
    $user = Auth::user();
    if ($user->is_superadmin) { return $next($request); }

    $escola = $user->escola;
    if (!$escola || !$escola->plano) { return $next($request); }

    $planos = config('planos'); // Lê os planos da configuração
    $planoAtual = $planos[$escola->plano] ?? null;

    if (!$planoAtual) { return $next($request); }

    // Loop para verificar múltiplos recursos (ex: aluno,professor)
    foreach ($recursos as $recurso) {
        $limite = $planoAtual[$recurso] ?? INF;

        if ($limite === INF) { continue; }

        $contagemAtual = 0;
        switch ($recurso) {
            case 'aluno': $contagemAtual = User::where('escola_id', $escola->id)->where('role', 'aluno')->count(); break;
            case 'professor': $contagemAtual = User::where('escola_id', $escola->id)->where('role', 'professor')->count(); break;
            case 'coordenador': $contagemAtual = User::where('escola_id', $escola->id)->where('role', 'coordenador')->count(); break;
            case 'questoes': $contagemAtual = Questao::whereHas('disciplina', fn($q) => $q->where('escola_id', $escola->id))->count(); break;
        }

        if ($contagemAtual >= $limite) {
            $nomeRecurso = ucfirst($recurso) . 's';
            $mensagem = "Limite de {$nomeRecurso} ({$limite}) para o {$planoAtual['display_name']} foi atingido. Para adicionar mais, por favor, faça um upgrade do plano.";
            return redirect()->back()->with('error', $mensagem);
        }
    }

    return $next($request);
}
}