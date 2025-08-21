<x-app-layout>
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">Detalhes da Avaliação</h1>
            <a href="{{ route('coordenador.modelos.index') }}" class="btn btn-outline-secondary">&larr; Voltar à Lista</a>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">{{ $modelo->nome }}</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Tipo:</strong> <span class="badge bg-primary">{{ ucfirst($modelo->tipo) }}</span></p>
                        <p><strong>Série:</strong> {{ $modelo->serie->nome }}</p>
                        <p><strong>Criada por:</strong> {{ $modelo->criador->nome }}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Nº de Questões (por aluno):</strong> {{ collect($modelo->regras_selecao['disciplinas'] ?? [])->sum(fn($d) => collect($d['questoes_por_nivel'])->sum('quantidade')) }}</p>
                        <p><strong>Tempo Limite:</strong> {{ $modelo->tempo_limite ? $modelo->tempo_limite . ' minutos' : 'Livre' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-header"><h5 class="mb-0">Resultados e Estatísticas</h5></div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-6 border-end">
                        <div class="p-3">
                            <h6>Participantes</h6>
                            <p class="display-5 fw-bold text-primary">{{ $stats['total_realizadas'] }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3">
                            <h6>Média da Turma</h6>
                            <p class="display-5 fw-bold text-primary">{{ number_format($stats['media_geral'], 2, ',', '.') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header"><h5 class="mb-0">Notas Individuais</h5></div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Aluno(a)</th>
                                <th>Data de Realização</th>
                                <th>Status</th>
                                <th>Nota Final</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
    @forelse ($resultados as $resultado)
        <tr>
            <td>{{ $resultado->aluno->nome }}</td>
            <td>{{ $resultado->data_realizacao->format('d/m/Y H:i') }}</td>
            <td>
                {{-- Lógica de exibição do Status --}}
                @if($resultado->status == 'Finalizado')
                    <span class="badge bg-success">Finalizado</span>
                @elseif($resultado->status == 'Aguardando Correção')
                    <span class="badge bg-warning text-dark">Aguardando Correção</span>
                @else
                    <span class="badge bg-secondary">{{ $resultado->status }}</span>
                @endif
            </td>
            <td><strong>{{ $resultado->nota !== null ? number_format($resultado->nota, 2, ',', '.') : 'N/A' }}</strong></td>
            <td>
                {{-- Lógica de exibição do Botão de Ação --}}
                @if($resultado->status == 'Aguardando Correção')
                    <a href="{{ route('coordenador.correcao.show', $resultado) }}" class="btn btn-sm btn-primary">Corrigir</a>
                @elseif($resultado->status == 'Finalizado')
                     <a href="{{ route('coordenador.correcao.show', $resultado) }}" class="btn btn-sm btn-outline-success"><i class="bi bi-check-lg"></i> Revisado</a>
                @else
                    <span class="text-muted small">--</span>
                @endif
            </td>
        </tr>
    @empty
        <tr><td colspan="5" class="text-center text-muted py-4">Nenhum aluno respondeu a esta avaliação ainda.</td></tr>
    @endforelse
</tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>