@section('title', 'Painel Painel do Aluno')
<x-app-layout>
    <div class="container">
        <h1 class="h3 mb-2 text-gray-800">Painel Principal</h1>
        <p class="mb-4">Bem-vindo(a) ao seu ambiente de estudos e avaliações.</p>

        <div class="alert alert-success">
            Bem-vindo(a) de volta, {{ Auth::user()->nome }}!
        </div>

        <div class="row">
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body text-center d-flex flex-column">
                        <div class="display-4 text-primary mb-2"><i class="bi bi-file-earmark-text-fill"></i></div>
                        <h5 class="card-title">Avaliações Disponíveis</h5>
                        <p class="card-text text-muted small flex-grow-1">Veja a lista de provas e simulados que você precisa responder.</p>
                        <a href="{{ route('aluno.avaliacoes.index') }}" class="btn btn-primary mt-auto">Ver Avaliações</a>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body text-center d-flex flex-column">
                        <div class="display-4 text-success mb-2"><i class="bi bi-graph-up-arrow"></i></div>
                        <h5 class="card-title">Meus Resultados</h5>
                        <p class="card-text text-muted small flex-grow-1">Consulte suas notas e o histórico do seu desempenho no sistema.</p>
                        {{-- A rota para esta funcionalidade será criada no próximo passo --}}
                        <a href="{{ route('aluno.resultados.index') }}" class="btn btn-success mt-auto">Ver Notas</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>