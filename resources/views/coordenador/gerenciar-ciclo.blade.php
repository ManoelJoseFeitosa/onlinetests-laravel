@section('title', 'Gerenciar Ciclo')
<x-app-layout>
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">Gerenciar Ciclo Anual</h2>
            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">&larr; Voltar ao Painel</a>
        </div>

        <p class="text-muted">
            Crie um novo ano letivo para habilitar o cadastro de matrículas e avaliações. Ao criar um novo ano, ele se tornará o "ativo" e os anteriores serão arquivados automaticamente.
        </p>

        {{-- Alertas de sucesso ou erro --}}
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        
        <div class="row g-5 mt-2">
            <!-- Card para Criar Novo Ano Letivo -->
            <div class="col-md-5">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="bi bi-calendar-plus-fill me-2"></i>Criar Novo Ano Letivo</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('coordenador.ciclo.store') }}">
                            @csrf
                            <div class="mb-3">
                                <label for="ano_novo" class="form-label">Ano</label>
                                <input type="number" class="form-control" id="ano_novo" name="ano_novo" value="{{ old('ano_novo', $ano_atual) }}" required>
                                <div class="form-text">Digite o ano que deseja iniciar.</div>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-check-circle-fill me-2"></i>Iniciar Novo Ano
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Card para Listar Anos Letivos Existentes -->
            <div class="col-md-7">
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>Histórico de Anos Letivos</h5>
                    </div>
                    <div class="card-body">
                        @if ($anos_letivos->isNotEmpty())
                            <ul class="list-group">
                                @foreach ($anos_letivos as $ano)
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <strong>Ano: {{ $ano->ano }}</strong>
                                        @if ($ano->status == 'ativo')
                                            <span class="badge bg-success rounded-pill">Ativo</span>
                                        @else
                                            <span class="badge bg-secondary rounded-pill">Arquivado</span>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-center text-muted">Nenhum ano letivo foi criado para esta escola ainda.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>