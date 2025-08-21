<?php

namespace App\Http\Controllers;

use App\Models\AnoLetivo;
use App\Models\Escola;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Handle the incoming request.
     * Mostra o dashboard apropriado com base no perfil do usuário logado.
     */
    public function __invoke(Request $request)
    {
        $user = Auth::user();

        // 1. Se for Super Admin, vai para o painel de Super Admin
        if ($user->is_superadmin) {
            $escolas = Escola::withCount(['usuarios as alunos_count' => function ($query) {
                $query->where('role', 'aluno');
            }])->orderBy('nome')->get();
            return view('dashboard', ['escolas' => $escolas]);
        }

        // 2. Se for Coordenador, vai para o painel de Coordenador
        if ($user->role === 'coordenador') {
            $ano_letivo_ativo = AnoLetivo::where('escola_id', $user->escola_id)
                                       ->where('status', 'ativo')
                                       ->first();
            return view('coordenador.dashboard', ['ano_letivo_ativo' => $ano_letivo_ativo]);
        }

        // 3. Se for Professor, vai para o painel de Professor
        if ($user->role === 'professor') {
            // A view 'professor.dashboard' não precisa de dados especiais por enquanto
            return view('professor.dashboard');
        }

        // 4. Se for Aluno, vai para o painel de Aluno
        if ($user->role === 'aluno') {
            // A view 'aluno.dashboard' não precisa de dados especiais por enquanto
            return view('aluno.dashboard');
        }

        // 5. Se não for nenhum dos perfis acima, apenas retorna a view padrão
        return view('dashboard');
    }
}