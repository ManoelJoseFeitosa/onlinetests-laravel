<x-app-layout>
    <x-slot name="title">
        Avaliações Disponíveis
    </x-slot>

    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">Avaliações Disponíveis</h2>
            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">&larr; Voltar ao Painel</a>
        </div>
        <hr class="mt-0">
        
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        @if ($avaliacoes_disponiveis->isNotEmpty())
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                @foreach ($avaliacoes_disponiveis as $item)
                <div class="col">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">{{ $item['objeto']->nome }}</h5>
                            <div class="mb-3">
                                <span class="badge bg-primary">{{ ucfirst($item['objeto']->tipo) }}</span>
                            </div>
                            <p class="card-text small text-muted flex-grow-1">
                                <strong>Série:</strong> {{ $item['objeto']->serie->nome ?? 'N/A' }} <br>
                                <strong>Tempo:</strong> {{ $item['objeto']->tempo_limite ? $item['objeto']->tempo_limite . ' minutos' : 'Livre' }}
                            </p>
<div class="mt-auto text-center">
    @if ($item['status'] == 'Finalizado')
        <a href="{{ route('aluno.resultados.show', $item['resultado']) }}" class="btn btn-success w-100">
            <i class="bi bi-check-circle-fill"></i> Ver Resultado
        </a>
    @elseif ($item['status'] == 'Aguardando Correção')
        {{-- Prova enviada, aguardando o professor. Mostra o mesmo botão "Ver Resultado" --}}
        <a href="{{ route('aluno.resultados.show', $item['resultado']) }}" class="btn btn-success w-100">
            <i class="bi bi-check-circle-fill"></i> Ver Resultado
        </a>
    @elseif ($item['status'] == 'Iniciada')
        <a href="{{ route('aluno.avaliacoes.responder', $item['resultado']->avaliacao_id) }}" class="btn btn-warning w-100">
            <i class="bi bi-pencil-square"></i> Continuar Avaliação
        </a>
    @else {{-- Status 'Não Iniciada' --}}
        @if ($item['tipo_obj'] == 'modelo')
            <a href="{{ route('aluno.avaliacoes.iniciar', $item['objeto']) }}" class="btn btn-primary w-100">
                <i class="bi bi-play-circle-fill"></i> Iniciar Agora
            </a>
        @else {{-- Recuperação --}}
            <a href="{{ route('aluno.avaliacoes.responder', $item['objeto']) }}" class="btn btn-primary w-100">
                <i class="bi bi-pencil-square"></i> Responder Agora
            </a>
        @endif
    @endif
</div>
                        </div>
                        <div class="card-footer text-muted small text-center">
                            Criada por: {{ $item['objeto']->criador->nome ?? 'N/A' }}
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <div class="alert alert-info" role="alert">Nenhuma avaliação disponível para você no momento.</div>
        @endif
    </div>
</x-app-layout>