<?php

namespace App\Http\Controllers;

use App\Models\Disciplina;
use App\Models\ModeloAvaliacao;
use App\Models\Questao;
use App\Models\Resultado;
use App\Models\Serie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ModeloAvaliacaoController extends Controller
{
    /**
     * Mostra o formulário para criar um novo modelo de avaliação.
     */
    public function create(): View
    {
        $user = Auth::user();

        if ($user->role === 'coordenador') {
            $series = Serie::where('escola_id', $user->escola_id)->orderBy('nome')->get();
            $disciplinas = Disciplina::where('escola_id', $user->escola_id)->orderBy('nome')->get();
        } else { // Lógica para o professor
            $series = $user->seriesLecionadas()->orderBy('nome')->get();
            $disciplinas = $user->disciplinasLecionadas()->orderBy('nome')->get();
        }

        // Reutilizamos a view do coordenador pois o formulário é idêntico
        return view('coordenador.criar-modelo-avaliacao', [
            'series' => $series,
            'disciplinas' => $disciplinas,
        ]);
    }

    /**
     * Salva o novo modelo de avaliação no banco de dados.
     */
    public function store(Request $request)
    {
        // ... (seu código store está correto e permanece o mesmo)
        $request->validate([
            'nome_modelo' => 'required|string|max:150',
            'serie_id' => 'required|exists:series,id',
            'tipo_modelo' => 'required|in:prova,simulado',
            'tempo_limite' => 'nullable|integer|min:1',
        ]);
        $regras_selecao = ['disciplinas' => []];
        $i = 0;
        while ($request->has("disciplina_id_$i")) {
            $disciplina_id = $request->input("disciplina_id_$i");
            $regra_disciplina = ['id' => (int) $disciplina_id, 'questoes_por_assunto' => [], 'questoes_por_nivel' => [],];
            if ($request->tipo_modelo == 'prova' && $request->has("regra_{$i}_assunto_0")) {
                foreach($request->input("regra_{$i}_assunto_0") as $assunto) {
                    $regra_disciplina['questoes_por_assunto'][] = ['assunto' => $assunto, 'quantidade' => 0];
                }
            }
            foreach (['facil', 'media', 'dificil'] as $nivel) {
                $qtd = $request->input("regra_{$i}_nivel_{$nivel}_qtd");
                if ($qtd > 0) {
                    $regra_disciplina['questoes_por_nivel'][] = ['nivel' => $nivel, 'quantidade' => (int) $qtd];
                }
            }
            $regras_selecao['disciplinas'][] = $regra_disciplina;
            $i++;
        }
        ModeloAvaliacao::create([
            'nome' => $request->nome_modelo,
            'tipo' => $request->tipo_modelo,
            'tempo_limite' => $request->tempo_limite,
            'serie_id' => $request->serie_id,
            'regras_selecao' => $regras_selecao,
            'criador_id' => Auth::id(),
            'escola_id' => Auth::user()->escola_id,
        ]);
        return redirect()->route('dashboard')->with('success', 'Modelo de avaliação criado com sucesso!');
    }

    /**
     * Lista os modelos de avaliação, diferenciando por perfil.
     */
    public function index(): View
    {
        $user = Auth::user();
        $query = ModeloAvaliacao::with('criador', 'serie');

        if ($user->role === 'coordenador') {
            $query->where('escola_id', $user->escola_id);
            $viewName = 'coordenador.modelos.index'; // View do Coordenador
        } else { // Lógica para o Professor
            $query->where('criador_id', $user->id);
            $viewName = 'professor.minhas-avaliacoes'; // View do Professor
        }

        $modelos = $query->orderBy('nome')->get();
        
        return view($viewName, ['modelos' => $modelos]);
    }

    /**
     * Mostra os detalhes e resultados de um modelo de avaliação específico.
     */
    public function show(ModeloAvaliacao $modelo): View
    {
    if ($modelo->escola_id !== Auth::user()->escola_id) {
        abort(403);
    }

    // CORREÇÃO: Carrega os resultados com todas as relações necessárias
    $resultados = Resultado::whereHas('avaliacao', function ($query) use ($modelo) {
        $query->where('modelo_id', $modelo->id);
    })
    ->with(['aluno', 'respostas.questao']) // Carrega o aluno e as respostas com suas questões
    ->orderBy('nota', 'desc')
    ->get();

    $stats = [
        'total_realizadas' => $resultados->count(),
        'media_geral' => $resultados->where('status', 'Finalizado')->avg('nota') ?? 0,
    ];

    return view('coordenador.modelos.show', [
        'modelo' => $modelo,
        'resultados' => $resultados,
        'stats' => $stats
    ]);
    }

    /**
     * Exclui um modelo de avaliação.
     */
    public function destroy(ModeloAvaliacao $modelo)
    {
        // ... (seu código destroy está correto e permanece o mesmo)
        $user = Auth::user();
        if ($modelo->escola_id !== $user->escola_id) { abort(403); }
        if ($user->role === 'professor' && $modelo->criador_id !== $user->id) {
            abort(403, 'Você não tem permissão para excluir este modelo.');
        }
        $modelo->delete();
        $routeName = $user->role === 'coordenador' ? 'coordenador.modelos.index' : 'professor.avaliacoes.index';
        return redirect()->route($routeName)->with('success', 'Modelo de avaliação excluído com sucesso!');
    }

    // --- MÉTODOS DE API PARA O JAVASCRIPT ---

    public function getAssuntos(Request $request)
    {
        $request->validate([
            'disciplina_id' => 'required|integer',
            'serie_id' => 'required|integer'
        ]);

        $assuntos = Questao::where('disciplina_id', $request->disciplina_id)
            // CORREÇÃO: Altera a busca para funcionar com a relação de muitas séries
            ->whereHas('series', function ($query) use ($request) {
                $query->where('series.id', $request->serie_id);
            })
            // FIM DA CORREÇÃO
            ->whereHas('disciplina', fn($q) => $q->where('escola_id', Auth::user()->escola_id))
            ->distinct()
            ->orderBy('assunto')
            ->pluck('assunto');
            
        return response()->json(['assuntos' => $assuntos]);
    }

    public function getConteudoSimulado(Serie $serie)
    {
        // ... (seu código getConteudoSimulado está correto e permanece o mesmo)
        if ($serie->escola_id !== Auth::user()->escola_id) { abort(403); }
        $disciplinas = Disciplina::where('escola_id', $serie->escola_id)
            ->whereHas('questoes', fn($q) => $q->where('serie_id', $serie->id))
            ->select('id', 'nome')->get();
        return response()->json($disciplinas);
    }
}