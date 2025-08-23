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
            ->latest() // Ordena pelas mais recentes primeiro
            ->paginate(15); // Adiciona paginação

        return view('professor.banco-questoes', ['questoes' => $questoes]);
    }

    /**
     * Mostra o formulário para criar uma nova questão.
     */
    public function criarQuestao(): View
    {
        $user = Auth::user();

        // O professor só pode criar questões para as séries e disciplinas que leciona
        $disciplinas = $user->disciplinasLecionadas()->orderBy('nome')->get();
        $series = $user->seriesLecionadas()->orderBy('nome')->get();

        return view('professor.criar-questao', compact('disciplinas', 'series'));
    }

    /**
     * Salva a nova questão no banco de dados.
     * * CORREÇÃO: Adicionadas as regras de validação para todos os campos
     * que serão usados para criar a questão, incluindo 'serie_id'.
     */
    public function salvarQuestao(Request $request)
    {
        $validatedData = $request->validate([
            'disciplina_id' => 'required|exists:disciplinas,id',
            'serie_id' => 'required|exists:series,id',
            'assunto' => 'required|string|max:255',
            'tipo' => 'required|string|in:alternativa,dissertativa', // Exemplo de tipos de questão
            'nivel' => 'required|string|in:facil,medio,dificil', // Exemplo de níveis
            'texto_questao' => 'required|string',
            'imagem_alt' => 'nullable|string|max:255',
            'imagem_questao' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $caminhoImagem = null;
        if ($request->hasFile('imagem_questao')) {
            // Salva o arquivo em 'storage/app/public/uploads/questoes' e retorna o caminho
            $caminhoImagem = $request->file('imagem_questao')->store('uploads/questoes', 'public');
        }

        Questao::create([
            'disciplina_id' => $validatedData['disciplina_id'],
            'serie_id' => $validatedData['serie_id'],
            'assunto' => $validatedData['assunto'],
            'tipo' => $validatedData['tipo'],
            'nivel' => $validatedData['nivel'],
            'texto' => $validatedData['texto_questao'],
            'imagem_nome' => $caminhoImagem, // Salva o caminho completo retornado pelo store
            'imagem_alt' => $validatedData['imagem_alt'],
            // ... (resto dos campos)
            'criador_id' => Auth::id(),
        ]);

        return redirect()->route('professor.banco-questoes.index')->with('success', 'Questão criada com sucesso!');
    }

    /**
     * Mostra o formulário para editar uma questão existente.
     */
    public function editarQuestao(Questao $questao): View
    {
        // Garante que o professor só possa editar suas próprias questões
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
     * * CORREÇÃO: Adicionadas as regras de validação para a atualização.
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
            'tipo' => 'required|string|in:alternativa,dissertativa',
            'nivel' => 'required|string|in:facil,medio,dificil',
            'texto_questao' => 'required|string',
            'imagem_alt' => 'nullable|string|max:255',
            'imagem_questao' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $dadosParaAtualizar = $validatedData;

        if ($request->hasFile('imagem_questao')) {
            // Se quiser apagar a imagem antiga, adicione a lógica aqui:
            // if ($questao->imagem_nome) { Storage::disk('public')->delete($questao->imagem_nome); }

            $caminhoImagem = $request->file('imagem_questao')->store('uploads/questoes', 'public');
            $dadosParaAtualizar['imagem_nome'] = $caminhoImagem;
        }

        $questao->update($dadosParaAtualizar);

        return redirect()->route('professor.banco-questoes.index')->with('success', 'Questão atualizada com sucesso!');
    }

    public function listarBloqueios()
    {
        // Busca todos os resultados que estão com 'is_blocked' = true
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