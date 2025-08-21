<?php

namespace App\Http\Controllers;

use App\Models\AnoLetivo;
use App\Models\Disciplina;
use App\Models\Matricula;
use App\Models\Serie;
use App\Models\User;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class CoordenadorController extends Controller
{
    /**
     * Mostra a página de gerenciamento de ciclos (anos letivos).
     */
    public function gerenciarCiclo(): View
    {
        $escola_id = Auth::user()->escola_id;

        $anos_letivos = AnoLetivo::where('escola_id', $escola_id)
                                ->orderBy('ano', 'desc')
                                ->get();

        return view('coordenador.gerenciar-ciclo', [
            'anos_letivos' => $anos_letivos,
            'ano_atual' => now()->year
        ]);
    }

    /**
     * Salva um novo ciclo (ano letivo).
     */
    public function salvarCiclo(Request $request)
    {
        $ano_atual = now()->year;

        $request->validate([
            'ano_novo' => "required|integer|digits:4|min:{$ano_atual}",
        ]);

        $escola_id = Auth::user()->escola_id;
        $ano_novo = $request->input('ano_novo');

        $existente = AnoLetivo::where('ano', $ano_novo)->where('escola_id', $escola_id)->exists();

        if ($existente) {
            return back()->with('error', "O ano letivo {$ano_novo} já está cadastrado.");
        }

        DB::transaction(function () use ($escola_id, $ano_novo) {
            AnoLetivo::where('escola_id', $escola_id)
                     ->where('status', 'ativo')
                     ->update(['status' => 'arquivado']);

            AnoLetivo::create([
                'ano' => $ano_novo,
                'escola_id' => $escola_id,
                'status' => 'ativo'
            ]);
        });

        return back()->with('success', "Ano letivo {$ano_novo} cadastrado e ativado com sucesso!");
    }

    /**
     * Mostra a página de gerenciamento de usuários.
     */
    public function gerenciarUsuarios(): View
    {
        $escola_id = Auth::user()->escola_id;
        $usuarios = User::where('escola_id', $escola_id)->where('is_superadmin', false)->orderBy('nome')->get();
        $series = Serie::where('escola_id', $escola_id)->orderBy('nome')->get();
        $disciplinas = Disciplina::where('escola_id', $escola_id)->orderBy('nome')->get();
        
        return view('coordenador.gerenciar-usuarios', [
            'usuarios' => $usuarios,
            'series' => $series,
            'disciplinas' => $disciplinas
        ]);
    }

    /**
     * Salva um novo usuário (aluno, professor ou coordenador).
     */
    public function salvarUsuario(Request $request)
    {
        $escola_id = Auth::user()->escola_id;
        $ano_letivo_ativo = AnoLetivo::where('escola_id', $escola_id)->where('status', 'ativo')->first();
        $request->validate([
            'nome' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'senha' => 'required|string|min:6',
            'role' => 'required|string|in:aluno,professor,coordenador',
            'serie_id' => 'required_if:role,aluno|nullable|exists:series,id',
            'disciplinas_ids' => 'required_if:role,professor|nullable|array',
            'series_ids' => 'required_if:role,professor|nullable|array',
        ]);
        DB::transaction(function () use ($request, $escola_id, $ano_letivo_ativo) {
            $novoUsuario = User::create(['nome' => $request->nome, 'email' => $request->email, 'password' => Hash::make($request->senha), 'role' => $request->role, 'escola_id' => $escola_id,]);
            if ($request->role === 'aluno' && $request->filled('serie_id')) {
                if (!$ano_letivo_ativo) { throw new \Exception('Não é possível matricular um aluno sem um ano letivo ativo.'); }
                $novoUsuario->matriculas()->create(['serie_id' => $request->serie_id, 'ano_letivo_id' => $ano_letivo_ativo->id,]);
            }
            if ($request->role === 'professor') {
                $novoUsuario->disciplinasLecionadas()->sync($request->disciplinas_ids ?? []);
                $novoUsuario->seriesLecionadas()->sync($request->series_ids ?? []);
            }
        });
        return back()->with('success', 'Usuário cadastrado com sucesso!');
    }

    /**
     * Mostra o formulário para editar um usuário existente.
     */
    public function editarUsuario(User $user): View
    {
        $escola_id = Auth::user()->escola_id;
        if ($user->escola_id !== $escola_id) { abort(403); }
        $series = Serie::where('escola_id', $escola_id)->orderBy('nome')->get();
        $disciplinas = Disciplina::where('escola_id', $escola_id)->orderBy('nome')->get();
        $anos_letivos = AnoLetivo::where('escola_id', $escola_id)->orderBy('ano', 'desc')->get();
        return view('coordenador.editar-usuario', ['usuario' => $user, 'series' => $series, 'disciplinas' => $disciplinas, 'anos_letivos' => $anos_letivos,]);
    }

    /**
     * Atualiza os dados de um usuário no banco.
     */
    public function atualizarUsuario(Request $request, User $user)
    {
        if ($user->escola_id !== Auth::user()->escola_id) { abort(403); }
        $request->validate(['nome' => 'required|string|max:255', 'email' => 'required|email|max:255|unique:users,email,' . $user->id, 'senha' => 'nullable|string|min:6', 'disciplinas_ids' => 'nullable|array', 'series_ids' => 'nullable|array',]);
        DB::transaction(function () use ($request, $user) {
            $user->nome = $request->input('nome');
            $user->email = $request->input('email');
            if ($request->filled('senha')) {
                $user->password = Hash::make($request->input('senha'));
                $user->precisa_trocar_senha = true;
            }
            $user->save();
            if ($user->role === 'professor') {
                $user->disciplinasLecionadas()->sync($request->disciplinas_ids ?? []);
                $user->seriesLecionadas()->sync($request->series_ids ?? []);
            }
        });
        return redirect()->route('coordenador.usuarios.index')->with('success', 'Usuário atualizado com sucesso!');
    }

    /**
     * Salva ou atualiza a matrícula de um aluno.
     */
    public function salvarMatricula(Request $request)
    {
        $request->validate(['user_id' => 'required|exists:users,id', 'serie_id' => 'required|exists:series,id', 'ano_letivo_id' => 'required|exists:ano_letivos,id',]);
        $aluno = User::find($request->user_id);
        if ($aluno->escola_id !== Auth::user()->escola_id) { abort(403); }
        Matricula::updateOrCreate(['aluno_id' => $request->user_id, 'ano_letivo_id' => $request->ano_letivo_id,], ['serie_id' => $request->serie_id, 'status' => 'cursando']);
        return back()->with('success', "Matrícula do aluno {$aluno->nome} salva com sucesso!");
    }

    /**
     * Retorna dados de matrícula em formato JSON para o JavaScript.
     */
    public function getMatriculaPorAno(User $user, AnoLetivo $anoLetivo)
    {
        if ($user->escola_id !== Auth::user()->escola_id || $anoLetivo->escola_id !== Auth::user()->escola_id) { abort(403); }
        $matricula = Matricula::where('aluno_id', $user->id)->where('ano_letivo_id', $anoLetivo->id)->first();
        if ($matricula) {
            return response()->json(['matriculado' => true, 'serie_id' => $matricula->serie_id, 'status' => $matricula->status]);
        }
        return response()->json(['matriculado' => false, 'serie_id' => null, 'status' => 'Não Matriculado']);
    }

    // ### NOVOS MÉTODOS PARA GERENCIAMENTO ACADÊMICO ###

    /**
     * Mostra a página de gerenciamento acadêmico.
     */
    public function gerenciarAcademico(): View
    {
        $escola_id = Auth::user()->escola_id;
        $series = Serie::with('disciplinas')->where('escola_id', $escola_id)->orderBy('nome')->get();
        $disciplinas = Disciplina::where('escola_id', $escola_id)->orderBy('nome')->get();

        return view('coordenador.gerenciamento-academico', [
            'series' => $series,
            'disciplinas' => $disciplinas
        ]);
    }

    /**
     * Salva uma nova série no banco de dados.
     */
    public function salvarSerie(Request $request)
    {
        $escola_id = Auth::user()->escola_id;
        $request->validate(['nome' => 'required|string|max:100|unique:series,nome,NULL,id,escola_id,'.$escola_id,]);
        Serie::create(['nome' => $request->input('nome'), 'escola_id' => $escola_id,]);
        return back()->with('success', 'Série cadastrada com sucesso!');
    }

    /**
     * Atualiza uma série existente.
     */
    public function atualizarSerie(Request $request, Serie $serie)
    {
        if ($serie->escola_id !== Auth::user()->escola_id) { abort(403); }
        $request->validate(['nome_serie' => 'required|string|max:100']);
        $serie->update(['nome' => $request->nome_serie]);
        return back()->with('success', 'Série atualizada com sucesso!');
    }

    /**
     * Exclui uma série.
     */
    public function excluirSerie(Serie $serie)
    {
        if ($serie->escola_id !== Auth::user()->escola_id) { abort(403); }
        $serie->delete();
        return back()->with('success', 'Série excluída com sucesso!');
    }

    /**
     * Salva uma nova disciplina no banco de dados.
     */
    public function salvarDisciplina(Request $request)
    {
        $escola_id = Auth::user()->escola_id;
        $request->validate(['nome' => 'required|string|max:100|unique:disciplinas,nome,NULL,id,escola_id,'.$escola_id,]);
        Disciplina::create(['nome' => $request->input('nome'),'escola_id' => $escola_id,]);
        return back()->with('success', 'Disciplina cadastrada com sucesso!');
    }

    /**
     * Atualiza uma disciplina existente.
     */
    public function atualizarDisciplina(Request $request, Disciplina $disciplina)
    {
        if ($disciplina->escola_id !== Auth::user()->escola_id) { abort(403); }
        $request->validate(['nome_disciplina' => 'required|string|max:100']);
        $disciplina->update(['nome' => $request->nome_disciplina]);
        return back()->with('success', 'Disciplina atualizada com sucesso!');
    }

    /**
     * Exclui uma disciplina.
     */
    public function excluirDisciplina(Disciplina $disciplina)
    {
        if ($disciplina->escola_id !== Auth::user()->escola_id) { abort(403); }
        $disciplina->delete();
        return back()->with('success', 'Disciplina excluída com sucesso!');
    }

    /**
     * Associa disciplinas a uma série.
     */
    public function associarDisciplinas(Request $request)
    {
        $request->validate([
            'serie_id_associacao' => 'required|exists:series,id',
            'disciplinas_selecionadas' => 'nullable|array'
        ]);
        $serie = Serie::find($request->serie_id_associacao);
        if ($serie->escola_id !== Auth::user()->escola_id) { abort(403); }
        $serie->disciplinas()->sync($request->disciplinas_selecionadas ?? []);
        return back()->with('success', 'Disciplinas associadas à série com sucesso!');
    }

    /**
 * Mostra o painel de auditoria com filtros e paginação.
 */
public function painelAuditoria(Request $request): View
{
    $escola_id = Auth::user()->escola_id;
    $perPage = 20; // Itens por página

    // Começa a query base, buscando logs de usuários da escola
    $query = AuditLog::whereHas('user', function ($query) use ($escola_id) {
        $query->where('escola_id', $escola_id);
    });

    // Aplica o filtro de busca por email ou IP
    if ($request->filled('q')) {
        $searchQuery = $request->input('q');
        $query->where(function ($q) use ($searchQuery) {
            $q->where('user_email', 'like', "%{$searchQuery}%")
              ->orWhere('ip_address', 'like', "%{$searchQuery}%");
        });
    }

    // Aplica o filtro por tipo de ação
    if ($request->filled('action_filter')) {
        $query->where('action', $request->input('action_filter'));
    }

    // Busca todas as ações distintas para popular o dropdown de filtro
    $unique_actions = AuditLog::select('action')->distinct()->orderBy('action')->pluck('action');

    // Executa a query com paginação
    $logs = $query->latest('timestamp')->paginate($perPage)->withQueryString();

    return view('coordenador.painel-auditoria', [
        'logs' => $logs,
        'unique_actions' => $unique_actions,
        'search_query' => $request->input('q', ''),
        'action_filter' => $request->input('action_filter', '')
    ]);
}
}