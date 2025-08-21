<?php

namespace App\Http\Controllers;

use App\Models\AnoLetivo;
use App\Models\Questao;
use App\Models\Resultado;
use App\Models\Serie;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DesempenhoController extends Controller
{
    // Mostra a página principal do painel de desempenho
    public function index()
    {
        return view('coordenador.desempenho', ['escola_nome' => Auth::user()->escola->nome]);
    }

    // --- MÉTODOS DE API PARA O JAVASCRIPT ---

    public function getTurmas()
    {
    $user = Auth::user();

    // Se for coordenador, mostra todas as séries da escola.
    // Se for professor, mostra apenas as séries que ele leciona.
    if ($user->role === 'coordenador') {
        $series = Serie::where('escola_id', $user->escola_id)->orderBy('nome')->get(['id', 'nome']);
    } else {
        $series = $user->seriesLecionadas()->orderBy('nome')->get(['id', 'nome']);
    }

    return response()->json($series);
    }

    public function getAlunosPorTurma(Serie $serie)
    {
        if ($serie->escola_id !== Auth::user()->escola_id) abort(403);

        $ano_letivo_ativo = AnoLetivo::where('escola_id', Auth::user()->escola_id)->where('status', 'ativo')->first();
        if (!$ano_letivo_ativo) return response()->json([]);

        $alunos = User::whereHas('matriculas', fn($q) => $q->where('serie_id', $serie->id)->where('ano_letivo_id', $ano_letivo_ativo->id))
            ->orderBy('nome')->get(['id', 'nome']);
        return response()->json($alunos);
    }

    public function getDesempenhoTurma(Serie $serie)
    {
        if ($serie->escola_id !== Auth::user()->escola_id) abort(403);

        // Lógica para buscar a média de notas da turma por avaliação
        // Esta é uma query complexa e pode ser otimizada futuramente.
        $desempenho = []; // Placeholder

        return response()->json([
            'labels' => array_keys($desempenho),
            'data' => array_values($desempenho)
        ]);
    }

    public function getDesempenhoAluno(User $user)
    {
        if ($user->escola_id !== Auth::user()->escola_id) abort(403);

        // Gráfico 1: Evolução de Notas
        $resultados_gerais = $user->resultados()->with('avaliacao')->where('status', 'Finalizado')->orderBy('data_realizacao')->get();
        $desempenho_geral_data = [
            'labels' => $resultados_gerais->pluck('avaliacao.nome'),
            'data' => $resultados_gerais->pluck('nota'),
        ];

        // Gráfico 2: Percentual por Nível
        $desempenho_nivel = DB::table('respostas')
            ->join('questoes', 'respostas.questao_id', '=', 'questoes.id')
            ->whereIn('resultado_id', $user->resultados()->pluck('id'))
            ->select('questoes.nivel', DB::raw('count(*) as total'), DB::raw('SUM(CASE WHEN respostas.resposta_aluno = questoes.gabarito THEN 1 ELSE 0 END) as acertos'))
            ->groupBy('questoes.nivel')->get()->keyBy('nivel');

        $niveis = ['facil', 'media', 'dificil'];
        $desempenho_nivel_data = [
            'labels' => ['Fácil', 'Média', 'Difícil'],
            'data' => collect($niveis)->map(function ($nivel) use ($desempenho_nivel) {
                $stat = $desempenho_nivel->get($nivel);
                return ($stat && $stat->total > 0) ? round(($stat->acertos / $stat->total) * 100) : 0;
            })
        ];

        return response()->json(['geral' => $desempenho_geral_data, 'nivel' => $desempenho_nivel_data]);
    }
}