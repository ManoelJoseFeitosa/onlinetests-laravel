<x-app-layout>
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">Central de Relatórios</h2>
            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">&larr; Voltar ao Painel</a>
        </div>
        <p class="text-muted">Gere relatórios em PDF para análise e acompanhamento acadêmico.</p>
        <hr>

        <div class="row g-4">
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-header"><h5 class="mb-0"><i class="bi bi-people-fill me-2"></i>Alunos por Série</h5></div>
                    <div class="card-body d-flex flex-column">
                        <form action="{{ route('coordenador.relatorios.alunos_por_serie') }}" method="POST" target="_blank" class="d-flex flex-column flex-grow-1">
                            @csrf
                            <div class="mb-3"><label class="form-label">Ano Letivo</label><select class="form-select" name="ano_letivo_id" required>@foreach ($anos_letivos as $ano)<option value="{{ $ano->id }}">{{ $ano->ano }}</option>@endforeach</select></div>
                            <div class="mb-3 flex-grow-1"><label class="form-label">Série</label><select class="form-select" name="serie_id" required><option value="" disabled selected>-- Selecione --</option>@foreach ($series as $serie)<option value="{{ $serie->id }}">{{ $serie->nome }}</option>@endforeach</select></div>
                            <button type="submit" class="btn btn-primary w-100 mt-auto">Gerar PDF</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-header"><h5 class="mb-0"><i class="bi bi-file-earmark-text-fill me-2"></i>Boletim por Turma</h5></div>
                    <div class="card-body d-flex flex-column">
                        <form action="{{ route('coordenador.relatorios.boletim_turma') }}" method="POST" target="_blank" class="d-flex flex-column flex-grow-1">
                            @csrf
                            <div class="mb-3"><label class="form-label">Ano Letivo</label><select class="form-select" name="ano_letivo_id" required><option value="" disabled selected>-- Selecione --</option>@foreach ($anos_letivos as $ano)<option value="{{ $ano->id }}">{{ $ano->ano }}</option>@endforeach</select></div>
                            <div class="mb-3 flex-grow-1"><label class="form-label">Série</label><select class="form-select" name="serie_id" required><option value="" disabled selected>-- Selecione --</option>@foreach ($series as $serie)<option value="{{ $serie->id }}">{{ $serie->nome }}</option>@endforeach</select></div>
                            <button type="submit" class="btn btn-primary w-100 mt-auto">Gerar PDF</button>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-header"><h5 class="mb-0"><i class="bi bi-bar-chart-line-fill me-2"></i>Desempenho por Assunto</h5></div>
                    <div class="card-body d-flex flex-column">
                           <form action="{{ route('coordenador.relatorios.desempenho_por_assunto') }}" method="POST" target="_blank" class="d-flex flex-column flex-grow-1">
                            @csrf
                            <div class="mb-3"><label class="form-label">Ano Letivo</label><select name="ano_letivo_id" class="form-select" required><option value="" disabled selected>-- Selecione --</option>@foreach ($anos_letivos as $ano)<option value="{{ $ano->id }}">{{ $ano->ano }}</option>@endforeach</select></div>
                            <div class="mb-3"><label class="form-label">Série</label><select name="serie_id" class="form-select" required><option value="" disabled selected>-- Selecione --</option>@foreach ($series as $serie)<option value="{{ $serie->id }}">{{ $serie->nome }}</option>@endforeach</select></div>
                            <div class="mb-3 flex-grow-1"><label class="form-label">Disciplina</label><select name="disciplina_id" class="form-select" required><option value="" disabled selected>-- Selecione --</option>@foreach ($disciplinas as $disciplina)<option value="{{ $disciplina->id }}">{{ $disciplina->nome }}</option>@endforeach</select></div>
                            <button type="submit" class="btn btn-primary w-100 mt-auto">Gerar PDF</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-header"><h5 class="mb-0"><i class="bi bi-search me-2"></i>Análise de Itens</h5></div>
                    <div class="card-body d-flex flex-column">
                        <p class="small text-muted">Avalie a qualidade de cada questão.</p>
                        <form action="{{ route('coordenador.relatorios.analise_de_itens') }}" method="POST" target="_blank" class="d-flex flex-column flex-grow-1">
                            @csrf
                            <div class="mb-3"><label class="form-label">Ano Letivo</label><select name="ano_letivo_id" class="form-select" required><option value="" disabled selected>-- Selecione --</option>@foreach ($anos_letivos as $ano)<option value="{{ $ano->id }}">{{ $ano->ano }}</option>@endforeach</select></div>
                            <div class="mb-3 flex-grow-1"><label class="form-label">Modelo de Avaliação</label><select name="modelo_id" class="form-select" required><option value="" disabled selected>-- Selecione --</option>@foreach ($modelos_avaliacao as $modelo)<option value="{{ $modelo->id }}">{{ $modelo->nome }}</option>@endforeach</select></div>
                            <div class="d-grid mt-auto"><button type="submit" class="btn btn-primary">Gerar PDF</button></div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-header"><h5 class="mb-0"><i class="bi bi-layers-fill me-2"></i>Desempenho por Nível</h5></div>
                    <div class="card-body d-flex flex-column">
                           <form action="{{ route('coordenador.relatorios.desempenho_por_nivel') }}" method="POST" target="_blank" class="d-flex flex-column flex-grow-1">
                            @csrf
                            <div class="mb-3"><label class="form-label">Ano Letivo</label><select name="ano_letivo_id" class="form-select" required><option value="" disabled selected>-- Selecione --</option>@foreach ($anos_letivos as $ano)<option value="{{ $ano->id }}">{{ $ano->ano }}</option>@endforeach</select></div>
                            <div class="mb-3"><label class="form-label">Série</label><select name="serie_id" class="form-select" required><option value="" disabled selected>-- Selecione --</option>@foreach ($series as $serie)<option value="{{ $serie->id }}">{{ $serie->nome }}</option>@endforeach</select></div>
                            <div class="mb-3 flex-grow-1"><label class="form-label">Disciplina</label><select name="disciplina_id" class="form-select" required><option value="" disabled selected>-- Selecione --</option>@foreach ($disciplinas as $disciplina)<option value="{{ $disciplina->id }}">{{ $disciplina->nome }}</option>@endforeach</select></div>
                            <button type="submit" class="btn btn-primary w-100 mt-auto">Gerar PDF</button>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-header"><h5 class="mb-0"><i class="bi bi-diagram-3-fill me-2"></i>Comparativo por Disciplina</h5></div>
                    <div class="card-body d-flex flex-column">
                        <p class="small text-muted">Compare o desempenho de diferentes turmas.</p>
                         <form action="{{ route('coordenador.relatorios.comparativo_turmas') }}" method="POST" target="_blank" class="d-flex flex-column flex-grow-1">
                            @csrf
                            <div class="mb-3"><label class="form-label">Ano Letivo</label><select name="ano_letivo_id" class="form-select" required><option value="" disabled selected>-- Selecione --</option>@foreach ($anos_letivos as $ano)<option value="{{ $ano->id }}">{{ $ano->ano }}</option>@endforeach</select></div>
                            <div class="mb-3 flex-grow-1"><label class="form-label">Disciplina</label><select name="disciplina_id" class="form-select" required><option value="" disabled selected>-- Selecione --</option>@foreach ($disciplinas as $disciplina)<option value="{{ $disciplina->id }}">{{ $disciplina->nome }}</option>@endforeach</select></div>
                            <div class="d-grid mt-auto"><button type="submit" class="btn btn-primary">Gerar PDF</button></div>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-header"><h5 class="mb-0"><i class="bi bi-clipboard-data-fill me-2"></i>Saúde do Banco de Questões</h5></div>
                    <div class="card-body d-flex flex-column">
                        <p class="small text-muted">Visão geral de todas as questões cadastradas.</p>
                        <a href="{{ route('coordenador.relatorios.saude_banco_questoes') }}" target="_blank" class="btn btn-secondary w-100 mt-auto">Gerar Relatório</a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-header"><h5 class="mb-0"><i class="bi bi-person-video3 me-2"></i>Lista de Professores</h5></div>
                    <div class="card-body d-flex flex-column">
                        <p class="small text-muted">Gera uma lista com todos os professores da escola.</p>
                        <a href="{{ route('coordenador.relatorios.lista_professores') }}" target="_blank" class="btn btn-secondary w-100 mt-auto">Gerar PDF</a>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-header"><h5 class="mb-0"><i class="bi bi-graph-up-arrow me-2"></i>Resultados de Simulado</h5></div>
                    <div class="card-body d-flex flex-column">
                           <form action="{{ route('coordenador.relatorios.resultado_simulado') }}" method="POST" target="_blank" class="d-flex flex-column flex-grow-1">
                            @csrf
                            <div class="mb-3"><label class="form-label">Ano Letivo</label><select name="ano_letivo_id" class="form-select" required><option value="" disabled selected>-- Selecione --</option>@foreach ($anos_letivos as $ano)<option value="{{ $ano->id }}">{{ $ano->ano }}</option>@endforeach</select></div>
                            <div class="mb-3 flex-grow-1"><label class="form-label">Selecione o Simulado</label><select class="form-select" name="modelo_id" required><option value="" disabled selected>-- Selecione --</option>@foreach ($modelos_avaliacao as $modelo)@if($modelo->tipo == 'simulado')<option value="{{ $modelo->id }}">{{ $modelo->nome }}</option>@endif @endforeach</select></div>
                            <button type="submit" class="btn btn-primary w-100 mt-auto">Gerar PDF</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>