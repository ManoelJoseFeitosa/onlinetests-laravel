<?php

namespace App\Http\Controllers;

use App\Models\Questao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;

class ProfessorController extends Controller
{
    public function bancoQuestoes(): View
    {
        // CORREÇÃO: Carrega a relação com 'series' (plural)
        $questoes = Questao::where('criador_id', Auth::id())
            ->with('disciplina', 'series')
            ->latest()
            ->paginate(15);
        return view('professor.banco-questoes', ['questoes' => $questoes]);
    }

    public function criarQuestao(): View
    {
        $user = Auth::user();
        $disciplinas = $user->disciplinasLecionadas()->orderBy('nome')->get();
        $series = $user->seriesLecionadas()->orderBy('nome')->get();
        return view('professor.criar-questao', compact('disciplinas', 'series'));
    }

    public function salvarQuestao(Request $request)
    {
        $validatedData = $request->validate([
            'disciplina_id' => 'required|exists:disciplinas,id',
            'series_ids' => 'required|array|min:1', // Agora espera um array de series
            'series_ids.*' => 'exists:series,id', // Valida cada id no array
            'assunto' => 'required|string|max:255', 'tipo' => 'required|string', 'nivel' => 'required|string', 'texto_questao' => 'required|string', 'imagem_questao' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', 'imagem_alt' => 'nullable|string|max:255', 'opcao_a' => 'nullable|string', 'opcao_b' => 'nullable|string', 'opcao_c' => 'nullable|string', 'opcao_d' => 'nullable|string',
            'gabarito' => [Rule::requiredIf($request->input('tipo') !== 'discursiva'), 'nullable', 'string', 'max:255'],
            'justificativa_gabarito' => 'nullable|string',
        ]);

        $caminhoImagem = $request->hasFile('imagem_questao') ? $request->file('imagem_questao')->store('uploads/questoes', 'public') : null;

        $questao = Questao::create([
            'disciplina_id' => $validatedData['disciplina_id'],
            'assunto' => $validatedData['assunto'], 'tipo' => $validatedData['tipo'], 'nivel' => $validatedData['nivel'], 'texto' => $validatedData['texto_questao'], 'imagem_nome' => $caminhoImagem, 'imagem_alt' => $validatedData['imagem_alt'] ?? null, 'opcao_a' => $validatedData['opcao_a'] ?? null, 'opcao_b' => $validatedData['opcao_b'] ?? null, 'opcao_c' => $validatedData['opcao_c'] ?? null, 'opcao_d' => $validatedData['opcao_d'] ?? null,
            'gabarito' => $validatedData['gabarito'] ?? null,
            'justificativa_gabarito' => $validatedData['justificativa_gabarito'] ?? null,
            'criador_id' => Auth::id(),
        ]);

        // Associa a questão às séries selecionadas
        $questao->series()->sync($validatedData['series_ids']);

        return redirect()->route('professor.banco-questoes.index')->with('success', 'Questão criada com sucesso!');
    }

    public function editarQuestao(Questao $questao): View
    {
        if ($questao->criador_id !== Auth::id()) { abort(403); }
        $user = Auth::user();
        $disciplinas = $user->disciplinasLecionadas()->orderBy('nome')->get();
        $series = $user->seriesLecionadas()->orderBy('nome')->get();
        $questao->load('series'); // Carrega as séries já associadas
        return view('professor.editar-questao', compact('questao', 'disciplinas', 'series'));
    }

    public function atualizarQuestao(Request $request, Questao $questao)
    {
        if ($questao->criador_id !== Auth::id()) { abort(403); }
        $validatedData = $request->validate([
            'disciplina_id' => 'required|exists:disciplinas,id',
            'series_ids' => 'required|array|min:1', // Validação para múltiplas séries
            'series_ids.*' => 'exists:series,id',
            'assunto' => 'required|string|max:255', 'tipo' => 'required|string', 'nivel' => 'required|string', 'texto_questao' => 'required|string', 'imagem_questao' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', 'imagem_alt' => 'nullable|string|max:255', 'opcao_a' => 'nullable|string', 'opcao_b' => 'nullable|string', 'opcao_c' => 'nullable|string', 'opcao_d' => 'nullable|string',
            'gabarito' => [Rule::requiredIf($request->input('tipo') !== 'discursiva'), 'nullable', 'string', 'max:255'],
            'justificativa_gabarito' => 'nullable|string',
        ]);

        $dadosParaAtualizar = $validatedData;
        $dadosParaAtualizar['texto'] = $validatedData['texto_questao'];
        unset($dadosParaAtualizar['texto_questao'], $dadosParaAtualizar['series_ids']);

        if ($request->hasFile('imagem_questao')) {
            if ($questao->imagem_nome) { Storage::disk('public')->delete($questao->imagem_nome); }
            $dadosParaAtualizar['imagem_nome'] = $request->file('imagem_questao')->store('uploads/questoes', 'public');
        }

        $questao->update($dadosParaAtualizar);
        $questao->series()->sync($validatedData['series_ids']); // Sincroniza as séries

        return redirect()->route('professor.banco-questoes.index')->with('success', 'Questão atualizada com sucesso!');
    }

    /**
 * Mostra a página inicial para gerenciar notas, com o seletor de turmas.
 */
public function gerenciarNotas(): View
{
    $series = Auth::user()->seriesLecionadas()->orderBy('nome')->get();
    return view('professor.notas.index', compact('series'));
}

/**
 * Busca os dados (alunos, avaliações, resultados) para montar o boletim de uma turma.
 */
public function buscarDadosBoletim(Serie $serie)
{
    // Garante que o professor só acesse turmas que ele leciona
    if (!Auth::user()->seriesLecionadas->contains($serie)) {
        abort(403);
    }

    // Pega todos os alunos matriculados na série
    $alunos = $serie->alunos()->orderBy('nome')->get();

    // Pega todas as avaliações que os alunos desta série já realizaram
    $avaliacoes = Avaliacao::whereHas('resultados.aluno.matriculas', function ($query) use ($serie) {
        $query->where('serie_id', $serie->id);
    })->orderBy('nome')->get();

    // Pega todos os resultados dos alunos desta série de uma vez para otimizar a busca
    $resultados = Resultado::whereIn('aluno_id', $alunos->pluck('id'))
                           ->whereIn('avaliacao_id', $avaliacoes->pluck('id'))
                           ->get()
                           ->keyBy(function ($item) {
                               return $item['aluno_id'] . '-' . $item['avaliacao_id'];
                           });

    return response()->json([
        'alunos' => $alunos,
        'avaliacoes' => $avaliacoes,
        'resultados' => $resultados,
    ]);
}

/**
 * Atualiza a nota de um resultado específico.
 */
public function atualizarNota(Request $request, Resultado $resultado)
{
    // Validação
    $dadosValidados = $request->validate([
        'nova_nota' => 'required|numeric|min:0|max:10',
        'justificativa' => 'nullable|string|max:500',
    ]);

    // Lógica de auditoria
    // Se a nota_original ainda não foi definida, salva a nota atual antes de alterar.
    if (is_null($resultado->nota_original)) {
        $resultado->nota_original = $resultado->nota;
    }

    $resultado->nota = $dadosValidados['nova_nota'];
    $resultado->justificativa_ajuste = $dadosValidados['justificativa'];
    $resultado->nota_ajustada_por = Auth::id();
    $resultado->save();

    return response()->json([
        'success' => true,
        'message' => 'Nota atualizada com sucesso!',
        'nova_nota_formatada' => number_format($resultado->nota, 2, ',', '.')
    ]);
}
}