<?php

use App\Http\Controllers\AlunoController;
use App\Http\Controllers\CoordenadorController;
use App\Http\Controllers\CorrecaoController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DesempenhoController;
use App\Http\Controllers\ModeloAvaliacaoController;
use App\Http\Controllers\PrimeiroAcessoController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProfessorController;
use App\Http\Controllers\RecuperacaoController;
use App\Http\Controllers\RelatorioController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\ContatoController;
use App\Models\Documento;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rotas Públicas e de Autenticação
|--------------------------------------------------------------------------
*/
Route::get('/', function () { return view('welcome'); })->name('home');
Route::get('/funcionalidades', function () { return view('funcionalidades'); })->name('funcionalidades');

// Rota de Planos com dados definidos diretamente para evitar cache
Route::get('/planos', function () {
    $planos = [
        'essencial' => ['display_name' => 'Plano Essencial', 'questoes' => 1000, 'professor' => 10, 'aluno' => 500, 'coordenador' => 1, 'preco' => 'R$ 149,90', 'suporte' => 'Suporte via email'],
        'profissional' => ['display_name' => 'Plano Profissional', 'questoes' => 3000, 'professor' => 20, 'aluno' => 1000, 'coordenador' => 2, 'preco' => 'R$ 249,90', 'suporte' => 'Suporte via email'],
        'enterprise' => ['display_name' => 'Plano Enterprise', 'questoes' => INF, 'professor' => INF, 'aluno' => INF, 'coordenador' => INF, 'preco' => 'R$ 449,90', 'suporte' => 'Suporte via email e telefone']
    ];
    return view('planos', ['planos' => $planos]);
})->name('planos');

Route::get('/documentos', function () {
    $documentos = Documento::where('ativo', true)->latest()->get();
    return view('documentos', ['documentos' => $documentos]);
})->name('documentos');
Route::get('/contato', [ContatoController::class, 'create'])->name('contato');
Route::post('/contato', [ContatoController::class, 'store'])->name('contato.store');
Route::get('/politica-de-privacidade', function () { return view('politica-de-privacidade'); })->name('politica.privacidade');

require __DIR__.'/auth.php';

/*
|--------------------------------------------------------------------------
| Rotas do Sistema (Acessíveis APENAS após login)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    // Primeiro Acesso
    Route::get('/primeiro-acesso/trocar-senha', [PrimeiroAcessoController::class, 'create'])->name('primeiro-acesso.form');
    Route::post('/primeiro-acesso/trocar-senha', [PrimeiroAcessoController::class, 'store'])->name('primeiro-acesso.store');
    
    // Edição de Perfil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Rotas do Super Administrador
    Route::prefix('superadmin')->name('superadmin.')->group(function () {
        Route::get('/escolas/nova', [SuperAdminController::class, 'create'])->name('escolas.create');
        Route::post('/escolas', [SuperAdminController::class, 'store'])->name('escolas.store');
        Route::get('/escolas/{escola}/editar', [SuperAdminController::class, 'edit'])->name('escolas.edit');
        Route::put('/escolas/{escola}', [SuperAdminController::class, 'update'])->name('escolas.update');
        Route::patch('/escolas/{escola}/toggle-status', [SuperAdminController::class, 'toggleStatus'])->name('escolas.toggleStatus');
    });

    // Rotas do Coordenador
    Route::prefix('coordenador')->name('coordenador.')->group(function () {
        Route::get('/gerenciar-ciclo', [CoordenadorController::class, 'gerenciarCiclo'])->name('ciclo.index');
        Route::post('/gerenciar-ciclo', [CoordenadorController::class, 'salvarCiclo'])->name('ciclo.store');
        Route::get('/gerenciar-usuarios', [CoordenadorController::class, 'gerenciarUsuarios'])->name('usuarios.index');
        Route::post('/gerenciar-usuarios', [CoordenadorController::class, 'salvarUsuario'])->name('usuarios.store')->middleware('check.plan:aluno,professor');
        Route::get('/usuarios/{user}/editar', [CoordenadorController::class, 'editarUsuario'])->name('usuarios.edit');
        Route::put('/usuarios/{user}', [CoordenadorController::class, 'atualizarUsuario'])->name('usuarios.update');
        Route::post('/matricula/salvar', [CoordenadorController::class, 'salvarMatricula'])->name('matricula.store');
        Route::get('/api/matricula/{user}/{anoLetivo}', [CoordenadorController::class, 'getMatriculaPorAno'])->name('api.matricula.show');
        Route::get('/gerenciamento-academico', [CoordenadorController::class, 'gerenciarAcademico'])->name('academico.index');
        Route::post('/series', [CoordenadorController::class, 'salvarSerie'])->name('series.store');
        Route::put('/series/{serie}', [CoordenadorController::class, 'atualizarSerie'])->name('series.update');
        Route::delete('/series/{serie}', [CoordenadorController::class, 'excluirSerie'])->name('series.destroy');
        Route::post('/disciplinas', [CoordenadorController::class, 'salvarDisciplina'])->name('disciplinas.store');
        Route::put('/disciplinas/{disciplina}', [CoordenadorController::class, 'atualizarDisciplina'])->name('disciplinas.update');
        Route::delete('/disciplinas/{disciplina}', [CoordenadorController::class, 'excluirDisciplina'])->name('disciplinas.destroy');
        Route::post('/associar-disciplinas', [CoordenadorController::class, 'associarDisciplinas'])->name('academico.associar');
        Route::get('/auditoria', [CoordenadorController::class, 'painelAuditoria'])->name('auditoria.index');
        Route::prefix('modelos-avaliacao')->name('modelos.')->group(function () {
            Route::get('/', [ModeloAvaliacaoController::class, 'index'])->name('index');
            Route::get('/criar', [ModeloAvaliacaoController::class, 'create'])->name('create');
            Route::post('/', [ModeloAvaliacaoController::class, 'store'])->name('store');
            Route::get('/{modelo}', [ModeloAvaliacaoController::class, 'show'])->name('show');
            Route::delete('/{modelo}', [ModeloAvaliacaoController::class, 'destroy'])->name('destroy');
            Route::get('/api/assuntos', [ModeloAvaliacaoController::class, 'getAssuntos'])->name('api.assuntos');
            Route::get('/api/conteudo-simulado/{serie}', [ModeloAvaliacaoController::class, 'getConteudoSimulado'])->name('api.conteudo-simulado');
        });
        Route::prefix('recuperacoes')->name('recuperacoes.')->group(function () {
            Route::get('/criar', [RecuperacaoController::class, 'create'])->name('create');
            Route::post('/', [RecuperacaoController::class, 'store'])->name('store');
            Route::get('/api/buscar-questoes', [RecuperacaoController::class, 'buscarQuestoes'])->name('api.buscar-questoes');
        });
        Route::prefix('relatorios')->name('relatorios.')->group(function () {
            Route::get('/', [RelatorioController::class, 'index'])->name('index');
            Route::post('/alunos-por-serie', [RelatorioController::class, 'alunosPorSerie'])->name('alunos_por_serie');
            Route::post('/boletim-turma', [RelatorioController::class, 'boletimTurma'])->name('boletim_turma');
            Route::post('/desempenho-por-assunto', [RelatorioController::class, 'desempenhoPorAssunto'])->name('desempenho_por_assunto');
            Route::post('/analise-de-itens', [RelatorioController::class, 'analiseDeItens'])->name('analise_de_itens');
            Route::post('/desempenho-por-nivel', [RelatorioController::class, 'desempenhoPorNivel'])->name('desempenho_por_nivel');
            Route::post('/comparativo-turmas', [RelatorioController::class, 'comparativoTurmas'])->name('comparativo_turmas');
            Route::get('/saude-banco-questoes', [RelatorioController::class, 'saudeBancoQuestoes'])->name('saude_banco_questoes');
            Route::get('/lista-professores', [RelatorioController::class, 'listaProfessores'])->name('lista_professores');
            Route::post('/resultado-simulado', [RelatorioController::class, 'resultadoSimulado'])->name('resultado_simulado');
        });
        Route::prefix('correcao')->name('correcao.')->group(function () {
            Route::get('/resultado/{resultado}', [CorrecaoController::class, 'show'])->name('show');
            Route::post('/resultado/{resultado}', [CorrecaoController::class, 'store'])->name('store');
        });
        Route::prefix('desempenho')->name('desempenho.')->group(function () {
            Route::get('/', [DesempenhoController::class, 'index'])->name('index');
            Route::get('/api/turmas', [DesempenhoController::class, 'getTurmas'])->name('api.turmas');
            Route::get('/api/turmas/{serie}/alunos', [DesempenhoController::class, 'getAlunosPorTurma'])->name('api.alunos');
            Route::get('/api/turmas/{serie}', [DesempenhoController::class, 'getDesempenhoTurma'])->name('api.turma.dados');
            Route::get('/api/alunos/{user}', [DesempenhoController::class, 'getDesempenhoAluno'])->name('api.aluno.dados');
        });
    });

    // Rotas do Professor
    Route::prefix('professor')->name('professor.')->group(function () {
        Route::get('/banco-questoes', [ProfessorController::class, 'bancoQuestoes'])->name('banco-questoes.index');
        Route::get('/banco-questoes/criar', [ProfessorController::class, 'criarQuestao'])->name('banco-questoes.create');
        Route::post('/banco-questoes', [ProfessorController::class, 'salvarQuestao'])->name('banco-questoes.store')->middleware('check.plan:questoes');
        Route::get('/banco-questoes/{questao}/editar', [ProfessorController::class, 'editarQuestao'])->name('banco-questoes.edit');
        Route::put('/banco-questoes/{questao}', [ProfessorController::class, 'atualizarQuestao'])->name('banco-questoes.update');
        Route::prefix('modelos-avaliacao')->name('modelos.')->group(function () {
            Route::get('/criar', [ModeloAvaliacaoController::class, 'create'])->name('create');
        });
        Route::prefix('recuperacoes')->name('recuperacoes.')->group(function () {
            Route::get('/criar', [RecuperacaoController::class, 'create'])->name('create');
        });
        Route::prefix('minhas-avaliacoes')->name('avaliacoes.')->group(function () {
            Route::get('/', [ModeloAvaliacaoController::class, 'index'])->name('index');
            Route::get('/{modelo}', [ModeloAvaliacaoController::class, 'show'])->name('show');
            Route::delete('/{modelo}', [ModeloAvaliacaoController::class, 'destroy'])->name('destroy');
        });
        Route::prefix('desempenho')->name('desempenho.')->group(function () {
            Route::get('/', [DesempenhoController::class, 'index'])->name('index');
        });
    });

    // Rotas do Aluno --
    Route::prefix('aluno')->name('aluno.')->group(function () {
        Route::get('/avaliacoes', [AlunoController::class, 'listarAvaliacoes'])->name('avaliacoes.index');
        Route::get('/avaliacoes/iniciar/{modeloAvaliacao}', [AlunoController::class, 'iniciarAvaliacaoDinamica'])->name('avaliacoes.iniciar');
        Route::get('/avaliacoes/responder/{avaliacao}', [AlunoController::class, 'responderAvaliacao'])->name('avaliacoes.responder');
        Route::post('/avaliacoes/responder/{avaliacao}', [AlunoController::class, 'salvarRespostas'])->name('avaliacoes.salvar');
        Route::get('/meus-resultados', [AlunoController::class, 'meusResultados'])->name('resultados.index');
        Route::get('/resultados/{resultado}', [AlunoController::class, 'verResultado'])->name('resultados.show');
    });
});