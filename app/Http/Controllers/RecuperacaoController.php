<?php

namespace App\Http\Controllers;

use App\Models\Avaliacao;
use App\Models\Disciplina;
use App\Models\Questao;
use App\Models\Serie;
use App\Models\AnoLetivo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class RecuperacaoController extends Controller
{
    /**
     * Mostra o formulário para criar uma nova prova de recuperação.
     */
    public function create(): View
    {
    $user = Auth::user();

    // Se for coordenador, busca tudo da escola.
    // Se for professor, busca apenas as séries e disciplinas associadas a ele.
    if ($user->role === 'coordenador') {
        $disciplinas = Disciplina::where('escola_id', $user->escola_id)->orderBy('nome')->get();
        $series = Serie::where('escola_id', $user->escola_id)->orderBy('nome')->get();
    } else { // Lógica para o professor
        $disciplinas = $user->disciplinasLecionadas()->orderBy('nome')->get();
        $series = $user->seriesLecionadas()->orderBy('nome')->get();
    }

    // Reutilizamos a mesma view para ambos os perfis!
    return view('coordenador.criar-recuperacao', [
        'disciplinas' => $disciplinas,
        'series' => $series,
    ]);
    }

    /**
     * Salva a nova prova de recuperação.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nome_avaliacao' => 'required|string|max:255',
            'tempo_limite' => 'nullable|integer|min:1',
            'disciplina_id' => 'required|exists:disciplinas,id',
            'serie_id' => 'required|exists:series,id',
            'alunos_ids' => 'required|array|min:1',
            'alunos_ids.*' => 'exists:users,id',
            'questoes_ids' => 'required|array|min:1',
            'questoes_ids.*' => 'exists:questoes,id',
        ]);

        $ano_letivo_ativo = AnoLetivo::where('escola_id', Auth::user()->escola_id)
                                   ->where('status', 'ativo')
                                   ->firstOrFail();

        $recuperacao = Avaliacao::create([
            'nome' => $request->nome_avaliacao,
            'tipo' => 'recuperacao',
            'tempo_limite' => $request->tempo_limite,
            'disciplina_id' => $request->disciplina_id,
            'serie_id' => $request->serie_id,
            'criador_id' => Auth::id(),
            'escola_id' => Auth::user()->escola_id,
            'ano_letivo_id' => $ano_letivo_ativo->id,
            'is_dinamica' => false,
        ]);

        $recuperacao->alunosDesignados()->sync($request->alunos_ids);
        $recuperacao->questoes()->sync($request->questoes_ids);

        return redirect()->route('coordenador.modelos.index')->with('success', 'Prova de recuperação criada e designada com sucesso!');
    }
    
    /**
     * API para buscar questões e retornar em JSON.
     */
    public function buscarQuestoes(Request $request)
    {
        $request->validate([
            'disciplina_id' => 'required|exists:disciplinas,id',
        ]);

        $query = Questao::where('disciplina_id', $request->disciplina_id)
            ->whereHas('disciplina', fn($q) => $q->where('escola_id', Auth::user()->escola_id));

        if ($request->filled('assunto')) {
            $query->where('assunto', 'like', '%' . $request->assunto . '%');
        }

        if ($request->filled('nivel')) {
            $query->where('nivel', $request->nivel);
        }

        $questoes = $query->select('id', 'assunto', 'nivel', 'texto')->limit(50)->get()->map(function($q){
            $q->texto_preview = mb_strimwidth($q->texto, 0, 100, "...");
            return $q;
        });

        return response()->json($questoes);
    }
}