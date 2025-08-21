<x-app-layout>
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @endpush

    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="display-6">Meu Desempenho 🚀</h1>
            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">&larr; Voltar ao Painel</a>
        </div>

        <ul class="nav nav-tabs" id="desempenhoTab" role="tablist">
            <li class="nav-item" role="presentation"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#provas-pane" type="button">Provas</button></li>
            <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#simulados-pane" type="button">Simulados</button></li>
            <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#recuperacao-pane" type="button">Recuperação</button></li>
            <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#disciplinas-pane" type="button">Desempenho por Disciplina</button></li>
        </ul>

        <div class="tab-content" id="desempenhoTabContent">

            <div class="tab-pane fade show active" id="provas-pane" role="tabpanel">
                <div class="p-4 bg-light border border-top-0 rounded-bottom">
                    @if ($stats['total_provas'] > 0)
                    <div class="row g-4">
                        <div class="col-lg-4"><div class="card text-center text-white bg-primary h-100"><div class="card-body d-flex flex-column justify-content-center"><h5 class="card-title">Média Geral (Provas)</h5><p class="display-4 fw-bold mb-0">{{ number_format($stats['media_provas'], 2, ',', '.') }}</p></div></div></div>
                        <div class="col-lg-4"><div class="card text-center h-100"><div class="card-body d-flex flex-column justify-content-center"><h5 class="card-title">Provas Concluídas</h5><p class="display-4 fw-bold mb-0">{{ $stats['total_provas'] }}</p></div></div></div>
                        <div class="col-lg-4 d-none d-lg-block"><div class="card bg-light border-0 h-100"><div class="card-body d-flex flex-column justify-content-center align-items-center"><i class="bi bi-graph-up-arrow" style="font-size: 3rem; color: #6c757d;"></i><p class="text-muted mt-2">Acompanhe sua evolução!</p></div></div></div>
                        <div class="col-12 mt-4">
                            <div class="card"><div class="card-header fw-bold">Evolução de Notas nas Provas</div><div class="card-body" style="min-height: 400px;"><canvas id="graficoProvas"></canvas></div></div>
                        </div>
                    </div>
                    @else
                    <div class="alert alert-info text-center">Você ainda não concluiu nenhuma prova.</div>
                    @endif
                </div>
            </div>

            <div class="tab-pane fade" id="simulados-pane" role="tabpanel">
                 <div class="p-4 bg-light border border-top-0 rounded-bottom">
                    @if ($stats['total_simulados'] > 0)
                    <div class="card"><div class="card-header fw-bold">Notas nos Simulados</div><div class="card-body" style="min-height: 400px;"><canvas id="graficoSimulados"></canvas></div></div>
                    @else
                    <div class="alert alert-info text-center">Você ainda não concluiu nenhum simulado.</div>
                    @endif
                </div>
            </div>

            <div class="tab-pane fade" id="recuperacao-pane" role="tabpanel">
                 <div class="p-4 bg-light border border-top-0 rounded-bottom">
                    @if ($stats['total_recuperacao'] > 0)
                    <div class="card"><div class="card-header fw-bold">Notas nas Recuperações</div><div class="card-body" style="min-height: 400px;"><canvas id="graficoRecuperacao"></canvas></div></div>
                    @else
                    <div class="alert alert-info text-center">Você ainda não concluiu nenhuma avaliação de recuperação.</div>
                    @endif
                </div>
            </div>

            <div class="tab-pane fade" id="disciplinas-pane" role="tabpanel">
                <div class="p-4 bg-light border border-top-0 rounded-bottom">
                    <div class="accordion" id="accordionDisciplinas">
                        @forelse ($dados_por_disciplina as $nome_disciplina => $dados)
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse_{{ Str::slug($nome_disciplina) }}">{{ $nome_disciplina }}<span class="ms-auto me-3 badge bg-dark rounded-pill">Média: {{ number_format($dados['media'], 1, ',', '.') }}</span></button>
                            </h2>
                            <div id="collapse_{{ Str::slug($nome_disciplina) }}" class="accordion-collapse collapse" data-bs-parent="#accordionDisciplinas">
                                <div class="accordion-body p-0">
                                    <ul class="list-group list-group-flush">
                                        @foreach ($dados['resultados'] as $res)
                                        <a href="{{ route('aluno.resultados.show', $res) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                            <div>{{ $res->avaliacao->nome }}<small class="d-block text-muted">{{ $res->data_realizacao->format('d/m/Y') }}</small></div>
                                            <span class="badge bg-primary rounded-pill fs-6">Nota: {{ number_format($res->nota, 1, ',', '.') }}</span>
                                        </a>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="alert alert-info text-center">Nenhum dado de desempenho por disciplina para exibir.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // GRÁFICO DE PROVAS (LINHA)
            const canvasProvas = document.getElementById('graficoProvas');
            if (canvasProvas) {
                const dataProvas = @json($chart_data_provas);
                if(dataProvas.datasets.length > 0) {
                    new Chart(canvasProvas.getContext('2d'), { type: 'line', data: dataProvas, options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true, max: 10 } }, plugins: { legend: { position: 'top' } } } });
                }
            }
            // GRÁFICO DE SIMULADOS (BARRAS)
            const canvasSimulados = document.getElementById('graficoSimulados');
            if (canvasSimulados) {
                const dataSimulados = @json($chart_simulados);
                if(dataSimulados.labels.length > 0) {
                    new Chart(canvasSimulados.getContext('2d'), { type: 'bar', data: { labels: dataSimulados.labels, datasets: [{ label: 'Nota', data: dataSimulados.data, backgroundColor: '#1cc88a' }] }, options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true, max: 10 } } } });
                }
            }
            // GRÁFICO DE RECUPERAÇÃO (BARRAS)
            const canvasRecuperacao = document.getElementById('graficoRecuperacao');
            if (canvasRecuperacao) {
                const dataRecuperacao = @json($chart_recuperacoes);
                 if(dataRecuperacao.labels.length > 0) {
                    new Chart(canvasRecuperacao.getContext('2d'), { type: 'bar', data: { labels: dataRecuperacao.labels, datasets: [{ label: 'Nota', data: dataRecuperacao.data, backgroundColor: '#f6c23e' }] }, options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true, max: 10 } } } });
                }
            }
        });
    </script>
    @endpush
</x-app-layout>