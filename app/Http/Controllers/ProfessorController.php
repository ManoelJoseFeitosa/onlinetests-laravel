<?php

namespace App\Http\Controllers;

// --- Classes importadas ---
use App\Models\Avaliacao;
use App\Models\Questao;
use App\Models\Resultado;
use App\Models\Serie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;

class ProfessorController extends Controller
{
    // ... (os outros métodos como bancoQuestoes, criarQuestao, etc., permanecem iguais)
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
    
    /**
     * Mostra a página para gerenciar notas. Agora, ela pode receber o ID da série pela URL.
     */
    public function gerenciarNotas(Request $request)
    {
        $professor = Auth::user();
        $series = $professor->seriesLecionadas()->orderBy('nome')->get();
        
        $serieSelecionada = null;
        $alunos = collect();
        $avaliacoes = collect();
        $resultadosMap = [];

        if ($request->has('serie_id')) {
            $serieId = $request->input('serie_id');
            // Busca a série apenas dentro da coleção de séries que o professor leciona (mais seguro)
            $serieSelecionada = $series->firstWhere('id', $serieId);
            
            if ($serieSelecionada) {
                try {
                    // Passo 1: Pega os alunos da turma.
                    $alunos = $serieSelecionada->alunos()->orderBy('nome')->get();
                    $alunoIds = $alunos->pluck('id');

                    // Passo 2: Apenas continua se a turma tiver alunos.
                    if ($alunoIds->isNotEmpty()) {
                        // Passo 3: Pega os resultados destes alunos.
                        $resultados = Resultado::whereIn('aluno_id', $alunoIds)->get();
                        $avaliacaoIds = $resultados->pluck('avaliacao_id')->unique()->filter();

                        // Passo 4: Apenas busca as avaliações se houver resultados.
                        if ($avaliacaoIds->isNotEmpty()) {
                            $avaliacoes = Avaliacao::whereIn('id', $avaliacaoIds)->orderBy('nome')->get();
                        }

                        // Passo 5: Monta o mapa de resultados para a view usar.
                        foreach ($resultados as $resultado) {
                            $key = $resultado->aluno_id . '-' . $resultado->avaliacao_id;
                            $resultadosMap[$key] = $resultado;
                        }
                    }
                } catch (\Exception $e) {
                    Log::error('Erro crítico ao carregar dados do boletim: ' . $e->getMessage());
                    return redirect()->route('professor.notas.index')->with('error', 'Não foi possível carregar os dados da turma. Tente novamente.');
                }
            }
        }

        return view('professor.notas.index', [
            'series' => $series,
            'serieSelecionada' => $serieSelecionada,
            'alunos' => $alunos,
            'avaliacoes' => $avaliacoes,
            'resultadosMap' => $resultadosMap,
        ]);
    }


    /**
     * Atualiza a nota de um resultado específico.
     */
    public function atualizarNota(Request $request, Resultado $resultado)
    {
        // ... (Este método permanece igual)
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

