<?php

namespace App\Http\Controllers;

// --- CORREÇÃO: Classes importadas ---
use App\Models\Avaliacao;
use App\Models\Questao;
use App\Models\Resultado;
use App\Models\Serie;
// --- FIM DA CORREÇÃO ---
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;

class ProfessorController extends Controller
{
    public function bancoQuestoes(): View
    {
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
            'series_ids' => 'required|array|min:1',
            'series_ids.*' => 'exists:series,id',
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

        $questao->series()->sync($validatedData['series_ids']);
        return redirect()->route('professor.banco-questoes.index')->with('success', 'Questão criada com sucesso!');
    }

    public function editarQuestao(Questao $questao): View
    {
        if ($questao->criador_id !== Auth::id()) { abort(403); }
        $user = Auth::user();
        $disciplinas = $user->disciplinasLecionadas()->orderBy('nome')->get();
        $series = $user->seriesLecionadas()->orderBy('nome')->get();
        $questao->load('series');
        return view('professor.editar-questao', compact('questao', 'disciplinas', 'series'));
    }

    public function atualizarQuestao(Request $request, Questao $questao)
    {
        if ($questao->criador_id !== Auth::id()) { abort(403); }
        $validatedData = $request->validate([
            'disciplina_id' => 'required|exists:disciplinas,id',
            'series_ids' => 'required|array|min:1',
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
        $questao->series()->sync($validatedData['series_ids']);
        return redirect()->route('professor.banco-questoes.index')->with('success', 'Questão atualizada com sucesso!');
    }

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
        if (!Auth::user()->seriesLecionadas->contains($serie)) {
            abort(403);
        }

        // --- CORREÇÃO DEFINITIVA ---
        try {
            // 1. Busca os alunos da série, selecionando apenas os campos necessários.
            $alunos = $serie->alunos()->orderBy('nome')->get(['id', 'nome']);

            // 2. Busca as avaliações que tiveram resultados para esta série.
            $avaliacoes = Avaliacao::where('serie_id', $serie->id)
                                    ->whereHas('resultados')
                                    ->orderBy('nome')
                                    ->get(['id', 'nome']);

            // 3. Busca os resultados de forma otimizada.
            $resultados = Resultado::whereIn('aluno_id', $alunos->pluck('id'))
                ->whereIn('avaliacao_id', $avaliacoes->pluck('id'))
                ->get(['id', 'aluno_id', 'avaliacao_id', 'nota']);

            // 4. Monta um mapa de resultados para acesso rápido (aluno_id-avaliacao_id => resultado).
            $resultadosMap = [];
            foreach ($resultados as $resultado) {
                $key = $resultado->aluno_id . '-' . $resultado->avaliacao_id;
                $resultadosMap[$key] = [
                    'id' => $resultado->id,
                    'nota' => $resultado->nota
                ];
            }

            // 5. Constrói a resposta JSON manualmente para garantir que não haja erros.
            $alunosData = $alunos->map(fn($aluno) => ['id' => $aluno->id, 'nome' => $aluno->nome]);
            $avaliacoesData = $avaliacoes->map(fn($av) => ['id' => $av->id, 'nome' => $av->nome]);

            return response()->json([
                'alunos' => $alunosData,
                'avaliacoes' => $avaliacoesData,
                'resultados' => $resultadosMap,
            ]);

        } catch (\Exception $e) {
            // Em caso de qualquer erro inesperado, retorna um erro 500 com uma mensagem.
            return response()->json(['error' => 'Ocorreu um erro interno ao buscar os dados.'], 500);
        }
        // --- FIM DA CORREÇÃO DEFINITIVA ---
    }


    /**
     * Atualiza a nota de um resultado específico.
     */
    public function atualizarNota(Request $request, Resultado $resultado)
    {
        $dadosValidados = $request->validate([
            'nova_nota' => 'required|numeric|min:0|max:10',
            'justificativa' => 'nullable|string|max:500',
        ]);
        
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

    public function listarBloqueios()
    {
        $professor = auth()->user();

        $resultados_bloqueados = Resultado::where('is_blocked', true)
            ->whereHas('avaliacao', function ($query) use ($professor) {
                $query->where('criador_id', $professor->id);
            })
            ->with(['aluno', 'avaliacao'])
            ->latest()
            ->get();

        return view('professor.bloqueios.index', [
            'resultados_bloqueados' => $resultados_bloqueados
        ]);
    }

    public function desbloquearProva(Resultado $resultado)
    {
        if ($resultado->avaliacao->criador_id !== Auth::id()) {
            abort(403, 'Acesso não autorizado.');
        }

        $resultado->update(['is_blocked' => false]);

        return redirect()->route('professor.bloqueios.index')
                         ->with('success', 'A avaliação do aluno(a) ' . $resultado->aluno->nome . ' foi desbloqueada com sucesso!');
    }
}

