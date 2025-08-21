<?php

namespace App\Http\Controllers;

use App\Models\Escola;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class SuperAdminController extends Controller
{
    // Constante para os planos, para manter o código organizado.
    private const PLANS = [
        'essencial' => ['display_name' => 'Plano Essencial'],
        'profissional' => ['display_name' => 'Plano Profissional'],
        'enterprise' => ['display_name' => 'Plano Enterprise']
    ];
    
    /**
     * Exibe o painel principal do Super Administrador com a lista de escolas.
     */
    public function dashboard(): View
    {
        // Busca todas as escolas e, para cada uma, conta quantos usuários
        // com a role 'aluno' estão associados a ela.
        $escolas = Escola::withCount(['usuarios as alunos_count' => function ($query) {
            $query->where('role', 'aluno');
        }])->orderBy('nome')->get();

        // Retorna a view do dashboard, passando a lista de escolas para ela.
        return view('dashboard', ['escolas' => $escolas]);
    }

    /**
     * Mostra o formulário para criar uma nova escola.
     */
    public function create(): View
    {
        // Retorna a view do formulário, passando a lista de planos para o select.
        return view('superadmin.nova-escola', ['plans' => self::PLANS]);
    }

    /**
     * Salva uma nova escola e seu coordenador no banco de dados.
     */
    public function store(Request $request)
    {
        // 1. Validação dos dados que vieram do formulário.
        $validatedData = $request->validate([
            'nome_escola' => 'required|string|max:150|unique:escolas,nome',
            'cnpj_escola' => 'nullable|string|max:18|unique:escolas,cnpj',
            'plano_escola' => 'required|string|in:essencial,profissional,enterprise',
            'media_recuperacao' => 'required|numeric|min:0|max:10',
            'nome_coordenador' => 'required|string|max:150',
            'email_coordenador' => 'required|email|max:150|unique:users,email',
            'senha_provisoria' => 'required|string|min:6',
        ]);

        // 2. Usar uma transação garante que, se algo der errado, nada é salvo.
        // Ou salva os dois (escola e usuário), ou não salva nenhum.
        DB::transaction(function () use ($validatedData) {
            // Cria a Escola no banco
            $escola = Escola::create([
                'nome' => $validatedData['nome_escola'],
                'cnpj' => $validatedData['cnpj_escola'],
                'plano' => $validatedData['plano_escola'],
                'media_recuperacao' => $validatedData['media_recuperacao'],
            ]);

            // Cria o Usuário Coordenador, já associando o id da escola criada acima
            User::create([
                'nome' => $validatedData['nome_coordenador'],
                'email' => $validatedData['email_coordenador'],
                'password' => Hash::make($validatedData['senha_provisoria']),
                'role' => 'coordenador',
                'escola_id' => $escola->id,
            ]);
        });

        // 3. Redireciona de volta para o dashboard com uma mensagem de sucesso.
        return redirect()->route('dashboard')->with('success', 'Escola e Coordenador cadastrados com sucesso!');
    }

    // ### MÉTODO 1: MOSTRAR FORMULÁRIO DE EDIÇÃO ###
    public function edit(Escola $escola): View
    {
        // O Laravel já buscou a $escola para nós através da rota.
        // Agora, buscamos o coordenador principal associado a ela.
        $coordenador = User::where('escola_id', $escola->id)
                            ->where('role', 'coordenador')
                            ->firstOrFail(); // firstOrFail garante que um coordenador seja encontrado.

        return view('superadmin.editar-escola', [
            'escola' => $escola,
            'coordenador' => $coordenador,
            'plans' => self::PLANS,
        ]);
    }

    // ### MÉTODO 2: ATUALIZAR OS DADOS ###
    public function update(Request $request, Escola $escola)
    {
        // Buscamos o coordenador para poder validar o email dele
        $coordenador = User::where('escola_id', $escola->id)
                           ->where('role', 'coordenador')
                           ->firstOrFail();

        // Validação dos dados, com regras 'unique' que ignoram o registro atual
        $validatedData = $request->validate([
            'nome_escola' => 'required|string|max:150|unique:escolas,nome,' . $escola->id,
            'cnpj_escola' => 'nullable|string|max:18|unique:escolas,cnpj,' . $escola->id,
            'plano_escola' => 'required|string|in:essencial,profissional,enterprise',
            'media_recuperacao' => 'required|numeric|min:0|max:10',
            'nome_coordenador' => 'required|string|max:150',
            'email_coordenador' => 'required|email|max:150|unique:users,email,' . $coordenador->id,
            'senha_coordenador' => 'nullable|string|min:6', // Senha é opcional
        ]);

        // Transação para garantir a integridade dos dados
        DB::transaction(function () use ($validatedData, $escola, $coordenador) {
            // Atualiza os dados da escola
            $escola->update([
                'nome' => $validatedData['nome_escola'],
                'cnpj' => $validatedData['cnpj_escola'],
                'plano' => $validatedData['plano_escola'],
                'media_recuperacao' => $validatedData['media_recuperacao'],
            ]);

            // Atualiza os dados do coordenador
            $coordenador->nome = $validatedData['nome_coordenador'];
            $coordenador->email = $validatedData['email_coordenador'];

            // Se uma nova senha foi informada, atualiza
            if (!empty($validatedData['senha_coordenador'])) {
                $coordenador->password = Hash::make($validatedData['senha_coordenador']);
                $coordenador->precisa_trocar_senha = true;
            }
            $coordenador->save();
        });

        return redirect()->route('dashboard')->with('success', 'Escola atualizada com sucesso!');
    }

    // ### MÉTODO 3: ATIVAR/BLOQUEAR ###
    public function toggleStatus(Escola $escola)
    {
        $escola->status = ($escola->status == 'ativo') ? 'bloqueado' : 'ativo';
        $escola->save();

        return back()->with('success', "Status da escola '{$escola->nome}' alterado com sucesso!");
    }
}