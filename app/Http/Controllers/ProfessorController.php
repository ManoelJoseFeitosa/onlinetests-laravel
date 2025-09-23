<?php

namespace App\Http\Controllers;

use App\Models\Questao;
use App\Models\Resultado; // Adicionado para gerir os resultados
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

    // --- MÉTODOS ADICIONADOS PARA GERIR BLOQUEIOS ---

    /**
     * Lista as avaliações de alunos que foram bloqueadas.
     */
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

    /**
     * Desbloqueia uma avaliação específica.
     */
    public function desbloquearProva(Resultado $resultado)
    {
        // Verificação de segurança: garante que o professor só pode desbloquear
        // provas que ele mesmo criou.
        if ($resultado->avaliacao->criador_id !== Auth::id()) {
            abort(403, 'Acesso não autorizado.');
        }

        $resultado->update(['is_blocked' => false]);

        return redirect()->route('professor.bloqueios.index')
                         ->with('success', 'A avaliação do aluno(a) ' . $resultado->aluno->nome . ' foi desbloqueada com sucesso!');
    }
}
