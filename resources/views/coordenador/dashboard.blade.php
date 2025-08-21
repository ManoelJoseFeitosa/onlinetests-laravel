@section('title', 'Painel do Coordenador')
<x-app-layout>
    <div class="container">
        <h1 class="h3 mb-2 text-gray-800">Painel Principal</h1>
        <p class="mb-4">Visão geral e atalhos para as principais funcionalidades do sistema.</p>

        <div class="alert alert-success">
            Bem-vindo(a) de volta, {{ Auth::user()->nome }}!
        </div>

        @if (!$ano_letivo_ativo)
        <div class="alert alert-warning">
            <strong>Atenção:</strong> Não há um ano letivo ativo. Algumas funcionalidades podem não estar disponíveis.
            <a href="{{ route('coordenador.ciclo.index') }}" class="alert-link">Clique aqui para gerenciar os ciclos.</a>
        </div>
        @endif

        <div class="row">

            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body text-center d-flex flex-column">
                        <div class="display-4 text-secondary mb-2"><i class="bi bi-arrow-repeat"></i></div>
                        <h5 class="card-title">Gerenciar Ciclo</h5>
                        <p class="card-text text-muted small flex-grow-1">Crie um novo ano letivo ou altere o status dos anos existentes.</p>
                        <a href="{{ route('coordenador.ciclo.index') }}" class="btn btn-secondary mt-auto">Administrar</a>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body text-center d-flex flex-column">
                        <div class="display-4 text-danger mb-2"><i class="bi bi-people-fill"></i></div>
                        <h5 class="card-title">Gerenciar Usuários</h5>
                        <p class="card-text text-muted small flex-grow-1">Cadastre, edite e gerencie o ciclo de vida de alunos e professores.</p>
                        <a href="{{ route('coordenador.usuarios.index') }}" class="btn btn-danger mt-auto">Acessar</a>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body text-center d-flex flex-column">
                        <div class="display-4 text-warning mb-2"><i class="bi bi-journal-bookmark-fill"></i></div>
                        <h5 class="card-title">Gerenciamento Acadêmico</h5>
                        <p class="card-text text-muted small flex-grow-1">Crie e visualize séries, disciplinas e o ciclo letivo da sua escola.</p>
                        <a href="{{ route('coordenador.academico.index') }}" class="btn btn-warning mt-auto">Organizar</a>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body text-center d-flex flex-column">
                        <div class="display-4 text-success mb-2"><i class="bi bi-file-earmark-plus-fill"></i></div>
                        <h5 class="card-title">Criar Modelo de Avaliação</h5>
                        <p class="card-text text-muted small flex-grow-1">Defina as regras para gerar provas e simulados dinâmicos.</p>
                        <a href="{{ route('coordenador.modelos.create') }}" class="btn btn-success mt-auto">Criar Modelo</a>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body text-center d-flex flex-column">
                        <div class="display-4 text-primary mb-2"><i class="bi bi-card-checklist"></i></div>
                        <h5 class="card-title">Visualizar Avaliações</h5>
                        <p class="card-text text-muted small flex-grow-1">Acompanhe todos os modelos e avaliações criadas no sistema.</p>
                        <a href="{{ route('coordenador.modelos.index') }}" class="btn btn-primary mt-auto">Ver Lista</a>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body text-center d-flex flex-column">
                        <div class="display-4 text-info mb-2"><i class="bi bi-arrow-clockwise"></i></div>
                        <h5 class="card-title">Criar Recuperação</h5>
                        <p class="card-text text-muted small flex-grow-1">Crie provas específicas para um grupo selecionado de alunos.</p>
                        <a href="{{ route('coordenador.recuperacoes.create') }}" class="btn btn-info mt-auto">Criar Recuperação</a>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body text-center d-flex flex-column">
                        <div class="display-4 text-secondary mb-2"><i class="bi bi-file-earmark-bar-graph-fill"></i></div>
                        <h5 class="card-title">Central de Relatórios</h5>
                        <p class="card-text text-muted small flex-grow-1">Gere relatórios em PDF para análise e acompanhamento acadêmico.</p>
                        <a href="{{ route('coordenador.relatorios.index') }}" class="btn btn-secondary mt-auto">Gerar Relatórios</a>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body text-center d-flex flex-column">
                        <div class="display-4 text-dark mb-2"><i class="bi bi-shield-lock-fill"></i></div>
                        <h5 class="card-title">Painel de Auditoria</h5>
                        <p class="card-text text-muted small flex-grow-1">Monitore ações críticas e visualize o histórico de atividades no sistema.</p>
                        <a href="{{ route('coordenador.auditoria.index') }}" class="btn btn-dark mt-auto">Acessar Auditoria</a>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body text-center d-flex flex-column">
                        <div class="display-4 text-success mb-2"><i class="bi bi-graph-up-arrow"></i></div>
                        <h5 class="card-title">Desempenho do Aluno</h5>
                        <p class="card-text text-muted small flex-grow-1">Analise graficamente o desempenho de alunos e turmas.</p>
                        <a href="{{ route('coordenador.desempenho.index') }}" class="btn btn-success mt-auto">Analisar</a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>