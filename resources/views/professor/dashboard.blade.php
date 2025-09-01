@section('title', 'Painel do Professor')
<x-app-layout>
    <div class="container">
        <h1 class="h3 mb-2 text-gray-800">Painel Principal</h1>
        
        <div class="alert alert-success">
            Bem-vindo(a) de volta, {{ Auth::user()->nome }}!
        </div>

        <div class="row">

            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body text-center d-flex flex-column">
                        <div class="display-4 text-dark mb-2"><i class="bi bi-journal-richtext"></i></div>
                        <h5 class="card-title">Meu Banco de Questões</h5>
                        <p class="card-text text-muted small flex-grow-1">Crie, visualize e edite todas as suas questões em um único lugar.</p>
                        <a href="{{ route('professor.banco-questoes.index') }}" class="btn btn-dark mt-auto">Acessar Banco</a>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body text-center d-flex flex-column">
                        <div class="display-4 text-success mb-2"><i class="bi bi-file-earmark-plus-fill"></i></div>
                        <h5 class="card-title">Criar Modelo de Avaliação</h5>
                        <p class="card-text text-muted small flex-grow-1">Defina as regras para gerar provas e simulados para suas turmas.</p>
                        <a href="{{ route('professor.modelos.create') }}" class="btn btn-success mt-auto">Criar Modelo</a>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body text-center d-flex flex-column">
                        <div class="display-4 text-info mb-2"><i class="bi bi-arrow-clockwise"></i></div>
                        <h5 class="card-title">Criar Recuperação</h5>
                        <p class="card-text text-muted small flex-grow-1">Crie provas específicas para um grupo selecionado de alunos.</p>
                        <a href="{{ route('professor.recuperacoes.create') }}" class="btn btn-info mt-auto">Criar Recuperação</a>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body text-center d-flex flex-column">
                        <div class="display-4 text-primary mb-2"><i class="bi bi-card-checklist"></i></div>
                        <h5 class="card-title">Ver Minhas Avaliações</h5>
                        <p class="card-text text-muted small flex-grow-1">Acompanhe o desempenho dos alunos nas avaliações que você criou.</p>
                        <a href="{{ route('professor.avaliacoes.index') }}" class="btn btn-primary mt-auto">Ver Lista</a>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body text-center d-flex flex-column">
                        <div class="display-4 text-success mb-2"><i class="bi bi-graph-up-arrow"></i></div>
                        <h5 class="card-title">Desempenho do Aluno</h5>
                        <p class="card-text text-muted small flex-grow-1">Analise graficamente o desempenho de alunos e turmas.</p>
                        <a href="{{ route('professor.desempenho.index') }}" class="btn btn-success mt-auto">Analisar</a>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body text-center d-flex flex-column">
                        <div class="display-4 text-warning mb-2"><i class="bi bi-unlock-fill"></i></div>
                        <h5 class="card-title">Desbloquear Prova</h5>
                        <p class="card-text text-muted small flex-grow-1">Veja a lista de alunos que tiveram a prova bloqueada e libere o acesso.</p>
                        <a href="{{ route('professor.bloqueios.index') }}" class="btn btn-warning mt-auto">Gerenciar Bloqueios</a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Gerenciar Notas</h5>
                        <p class="card-text">Visualize e altere as notas finais dos alunos por turma.</p>
                        <a href="{{ route('professor.notas.index') }}" class="btn btn-primary">Acessar</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>