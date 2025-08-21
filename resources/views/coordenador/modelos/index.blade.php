<x-app-layout>
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">Modelos e Recuperações</h2>
            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">&larr; Voltar ao Painel</a>
        </div>
        <p class="text-muted">Gerencie os modelos de avaliação e as recuperações criadas. Clique em "Gerenciar" para ver os resultados e fazer correções.</p>
        <hr class="mt-0">

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if ($modelos->isNotEmpty())
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                @foreach ($modelos as $modelo)
                <div class="col">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">{{ $modelo->nome }}</h5>
                            <div class="mb-3">
                                <span class="badge bg-{{ $modelo->tipo == 'simulado' ? 'success' : 'primary' }}">{{ ucfirst($modelo->tipo) }}</span>
                            </div>
                            <p class="card-text small text-muted flex-grow-1">
                                <strong>Série:</strong> {{ $modelo->serie->nome ?? 'N/A' }} <br>
                                <strong>Questões:</strong> {{ collect($modelo->regras_selecao['disciplinas'] ?? [])->sum(fn($d) => collect($d['questoes_por_nivel'])->sum('quantidade')) }} <br>
                                <strong>Tempo:</strong> {{ $modelo->tempo_limite ? $modelo->tempo_limite . ' minutos' : 'Livre' }}
                            </p>
                            <div class="mt-auto">
                                <a href="{{ route('coordenador.modelos.show', $modelo) }}" class="btn btn-dark w-100 mb-2">Gerenciar Modelo</a>
                                <form action="{{ route('coordenador.modelos.destroy', $modelo) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir este modelo?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger w-100">Excluir</button>
                                </form>
                            </div>
                        </div>
                        <div class="card-footer text-muted small text-center">
                            Criada por: {{ $modelo->criador->nome ?? 'N/A' }}
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <div class="alert alert-info" role="alert">
                Nenhum modelo de avaliação foi criado para esta escola ainda.
            </div>
        @endif
    </div>
</x-app-layout>