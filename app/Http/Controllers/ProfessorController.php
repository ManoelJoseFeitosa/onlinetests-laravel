<?php

namespace App\Http\Controllers;

use App\Models\Questao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Models\Serie;
use App\Models\Disciplina;
use Illuminate\Support\Facades\Storage;
use App\Models\Resultado;

class ProfessorController extends Controller
{
    /**
     * Mostra a lista de questões criadas pelo professor logado.
     */
    public function bancoQuestoes(): View
    {
        $questoes = Questao::where('criador_id', Auth::id())
            ->with('disciplina', 'serie')
            ->latest()
            ->paginate(15);

        return view('professor.banco-questoes', ['questoes' => $questoes]);
    }

    /**
     * Mostra o formulário para criar uma nova questão.
     */
    public function criarQuestao(): View
    {
        $user = Auth::user();
        $disciplinas = $user->disciplinasLecionadas()->orderBy('nome')->get();
        $series = $user->seriesLecionadas()->orderBy('nome')->get();

        return view('professor.criar-questao', compact('disciplinas', 'series'));
    }

    /**
     * Salva a nova questão no banco de dados.
     */
    public function salvarQuestao(Request $request)
    {
        // Validação usando os nomes dos campos que provavelmente estão no seu formulário
        $validatedData = $request->validate([
            'disciplina_id' => 'required|exists:disciplinas,id',
            'serie_id' => 'required|exists:series,id',
            'assunto' => 'required|string|max:255',
            'tipo' => 'required|string',
            'nivel' => 'required|string',
            'texto_questao' => 'required|string', // Validando o campo do formulário
            'imagem_questao' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'imagem_alt' => 'nullable|string|max:255',
            'opcao_a' => 'nullable|string',
            'opcao_b' => 'nullable|string',
            'opcao_c' => 'nullable|string',
            'opcao_d' => 'nullable|string',
            'gabarito' => 'required|string|max:255',
            'justificativa_gabarito' => 'nullable|string',
        ]);

        $caminhoImagem = null;
        if ($request->hasFile('imagem_questao')) {
            $caminhoImagem = $request->file('imagem_questao')->store('uploads/questoes', 'public');
        }

        // Mapeamento correto dos campos para o banco de dados
        Questao::create([
            'disciplina_id' => $validatedData['disciplina_id'],
            'serie_id' => $validatedData['serie_id'],
            'assunto' => $validatedData['assunto'],
            'tipo' => $validatedData['tipo'],
            'nivel' => $validatedData['nivel'],
            'texto' => $validatedData['texto_questao'], // Mapeando 'texto_questao' do form para a coluna 'texto'
            'imagem_nome' => $caminhoImagem,
            'imagem_alt' => $validatedData['imagem_alt'],
            'opcao_a' => $validatedData['opcao_a'] ?? null,
            'opcao_b' => $validatedData['opcao_b'] ?? null,
            'opcao_c' => $validatedData['opcao_c'] ?? null,
            'opcao_d' => $validatedData['opcao_d'] ?? null,
            'gabarito' => $validatedData['gabarito'],
            'justificativa_gabarito' => $validatedData['justificativa_gabarito'] ?? null,
            'criador_id' => Auth::id(),
        ]);

        return redirect()->route('professor.banco-questoes.index')->with('success', 'Questão criada com sucesso!');
    }

    /**
     * Mostra o formulário para editar uma questão existente.
     */
    public function editarQuestao(Questao $questao): View
    {
        if ($questao->criador_id !== Auth::id()) {
            abort(403, 'Acesso não autorizado.');
        }

        $user = Auth::user();
        $disciplinas = $user->disciplinasLecionadas()->orderBy('nome')->get();
        $series = $user->seriesLecionadas()->orderBy('nome')->get();

        return view('professor.editar-questao', compact('questao', 'disciplinas', 'series'));
    }

    /**
     * Atualiza a questão no banco de dados.
     */
    public function atualizarQuestao(Request $request, Questao $questao)
    {
        if ($questao->criador_id !== Auth::id()) {
            abort(403);
        }

        $validatedData = $request->validate([
            'disciplina_id' => 'required|exists:disciplinas,id',
            'serie_id' => 'required|exists:series,id',
            'assunto' => 'required|string|max:255',
            'tipo' => 'required|string',
            'nivel' => 'required|string',
            'texto_questao' => 'required|string', // Validando o campo do formulário
            'imagem_questao' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'imagem_alt' => 'nullable|string|max:255',
            'opcao_a' => 'nullable|string',
            'opcao_b' => 'nullable|string',
            'opcao_c' => 'nullable|string',
            'opcao_d' => 'nullable|string',
            'gabarito' => 'required|string|max:255',
            'justificativa_gabarito' => 'nullable|string',
        ]);

        // Mapeando 'texto_questao' para a coluna 'texto'
        $dadosParaAtualizar = $validatedData;
        $dadosParaAtualizar['texto'] = $validatedData['texto_questao'];
        unset($dadosParaAtualizar['texto_questao']); // Remove a chave original para evitar conflito

        if ($request->hasFile('imagem_questao')) {
            if ($questao->imagem_nome) {
                Storage::disk('public')->delete($questao->imagem_nome);
            }
            $caminhoImagem = $request->file('imagem_questao')->store('uploads/questoes', 'public');
            $dadosParaAtualizar['imagem_nome'] = $caminhoImagem;
        }

        $questao->update($dadosParaAtualizar);

        return redirect()->route('professor.banco-questoes.index')->with('success', 'Questão atualizada com sucesso!');
    }

    public function listarBloqueios()
    {
        $resultadosBloqueados = Resultado::with(['avaliacao', 'aluno'])
            ->where('is_blocked', true)
            ->get();

        return view('professor.bloqueios.index', compact('resultadosBloqueados'));
    }

    public function desbloquearProva(Resultado $resultado)
    {
        $resultado->is_blocked = false;
        $resultado->save();

        return back()->with('success', 'Prova do aluno ' . $resultado->aluno->nome . ' desbloqueada com sucesso!');
    }
}
