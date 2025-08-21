@section('title', 'Painel Criar Recuperação')
<x-app-layout>
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Criar Prova de Recuperação</h2>
            {{-- O ideal seria voltar para a lista de avaliações --}}
            <a href="{{ route('coordenador.modelos.index') }}" class="btn btn-outline-secondary">&larr; Voltar</a>
        </div>
        <p class="text-muted">Gere uma avaliação específica para um grupo de alunos, selecionando questões do seu banco.</p>
        <hr>

        <form method="POST" action="{{ route('coordenador.recuperacoes.store') }}" id="form-recuperacao">
            @csrf
            <div class="card shadow-sm mb-4">
                <div class="card-header"><h5 class="mb-0">1. Detalhes e Alunos Designados</h5></div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nome_avaliacao" class="form-label">Nome da Avaliação</label>
                                <input type="text" name="nome_avaliacao" id="nome_avaliacao" class="form-control" required placeholder="Ex: Recuperação Final - Biologia">
                            </div>
                            <div class="mb-3">
                                <label for="tempo_limite" class="form-label">Tempo Limite (em minutos)</label>
                                <input type="number" class="form-control" id="tempo_limite" name="tempo_limite" placeholder="Opcional">
                            </div>
                            <div class="mb-3">
                                <label for="disciplina_id" class="form-label">Disciplina da Recuperação</label>
                                <select class="form-select" id="disciplina_id" name="disciplina_id" required>
                                    <option value="" selected disabled>-- Selecione uma Disciplina --</option>
                                    @foreach ($disciplinas as $disciplina)
                                    <option value="{{ $disciplina->id }}">{{ $disciplina->nome }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="serie_id" class="form-label">Selecione a Série para listar os alunos</label>
                                <select id="serie_id" name="serie_id" class="form-select" required>
                                    <option value="" selected disabled>-- Selecione uma Série --</option>
                                    @foreach ($series as $serie)<option value="{{ $serie->id }}">{{ $serie->nome }}</option>@endforeach
                                </select>
                            </div>
                            <div id="lista_alunos_container" class="border p-3 rounded" style="display:none;">
                                <label class="form-label fw-bold">Alunos para Recuperação</label>
                                <div id="checkbox_alunos" class="mt-2" style="max-height: 160px; overflow-y: auto;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">2. Questões da Prova</h5>
                    <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#modalBuscarQuestoes" id="btn-abrir-modal" disabled>
                        <i class="bi bi-plus"></i> Adicionar Questão do Banco
                    </button>
                </div>
                <div class="card-body">
                    <p id="aviso-questoes" class="text-muted">Selecione uma disciplina para poder adicionar questões.</p>
                    <ul class="list-group" id="lista-questoes-selecionadas"></ul>
                </div>
            </div>

            <div class="text-center mt-4">
                <button type="submit" class="btn btn-primary btn-lg">Criar e Designar Recuperação</button>
            </div>
        </form>
    </div>

    <div class="modal fade" id="modalBuscarQuestoes" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Buscar Questões no Banco</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-8"><label for="filtro-assunto">Assunto</label><input type="text" id="filtro-assunto" class="form-control" placeholder="Digite para filtrar por assunto..."></div>
                        <div class="col-md-4"><label for="filtro-nivel">Nível</label><select id="filtro-nivel" class="form-select"><option value="">Todos</option><option value="facil">Fácil</option><option value="media">Média</option><option value="dificil">Difícil</option></select></div>
                    </div>
                    <button type="button" class="btn btn-info mb-3" id="btn-buscar-questoes">Buscar</button>
                    <hr>
                    <div id="container-resultados-busca" style="max-height: 400px; overflow-y: auto;">
                        <p class="text-muted">Nenhuma busca realizada.</p>
                    </div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button></div>
            </div>
        </div>
    </div>
</x-app-layout>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let questoesSelecionadasIds = new Set();
    const disciplinaSelect = document.getElementById('disciplina_id');
    const serieSelect = document.getElementById('serie_id');
    const btnAbrirModal = document.getElementById('btn-abrir-modal');
    const avisoQuestoes = document.getElementById('aviso-questoes');
    const listaAlunosContainer = document.getElementById('lista_alunos_container');
    const checkboxAlunos = document.getElementById('checkbox_alunos');
    const btnBuscarQuestoes = document.getElementById('btn-buscar-questoes');
    const containerResultadosBusca = document.getElementById('container-resultados-busca');
    const listaQuestoesSelecionadas = document.getElementById('lista-questoes-selecionadas');
    const formRecuperacao = document.getElementById('form-recuperacao');

    // Habilita botão de adicionar questão
    disciplinaSelect.addEventListener('change', function() {
        btnAbrirModal.disabled = !this.value;
        avisoQuestoes.style.display = this.value ? 'none' : 'block';
    });

    // Carrega alunos
    serieSelect.addEventListener('change', function() {
        const serieId = this.value;
        if (!serieId) {
            listaAlunosContainer.style.display = 'none';
            return;
        }
        checkboxAlunos.innerHTML = '<p class="text-muted">Carregando alunos...</p>';
        listaAlunosContainer.style.display = 'block';

        const url = "{{ route('coordenador.api.matricula.show', ['user' => 0, 'anoLetivo' => 0]) }}".replace('/api/matricula/0/0', `/api/alunos_por_serie/${serieId}`); // Reutilizando a rota de API de alunos

        fetch(url)
            .then(res => res.json())
            .then(data => {
                checkboxAlunos.innerHTML = '';
                if (data.alunos && data.alunos.length > 0) {
                    data.alunos.forEach(aluno => {
                        checkboxAlunos.innerHTML += `
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="alunos_ids[]" value="${aluno.id}" id="aluno_${aluno.id}">
                                <label class="form-check-label" for="aluno_${aluno.id}">${aluno.nome}</label>
                            </div>`;
                    });
                } else {
                    checkboxAlunos.innerHTML = '<p class="text-danger">Nenhum aluno encontrado nesta série.</p>';
                }
            }).catch(err => checkboxAlunos.innerHTML = '<p class="text-danger">Ocorreu um erro ao carregar os alunos.</p>');
    });

    // Lógica de busca de questões no modal
    btnBuscarQuestoes.addEventListener('click', function() {
        const disciplinaId = disciplinaSelect.value;
        const assunto = document.getElementById('filtro-assunto').value;
        const nivel = document.getElementById('filtro-nivel').value;

        containerResultadosBusca.innerHTML = '<p>Buscando...</p>';

        const url = new URL("{{ route('coordenador.recuperacoes.api.buscar-questoes') }}");
        url.searchParams.append('disciplina_id', disciplinaId);
        url.searchParams.append('assunto', assunto);
        url.searchParams.append('nivel', nivel);

        fetch(url)
            .then(res => res.json())
            .then(data => {
                containerResultadosBusca.innerHTML = '';
                if (data.length > 0) {
                    const table = document.createElement('table');
                    table.className = 'table table-hover';
                    table.innerHTML = '<thead><tr><th>Assunto</th><th>Nível</th><th>Início do Enunciado</th><th>Ação</th></tr></thead>';
                    const tbody = document.createElement('tbody');
                    data.forEach(q => {
                        const isAdded = questoesSelecionadasIds.has(q.id);
                        tbody.innerHTML += `
                            <tr>
                                <td>${q.assunto}</td>
                                <td>${q.nivel}</td>
                                <td>${q.texto_preview}</td>
                                <td><button type="button" class="btn btn-sm btn-primary btn-adicionar-questao" data-id="${q.id}" data-texto="${q.texto_preview}" data-assunto="${q.assunto}" ${isAdded ? 'disabled' : ''}>${isAdded ? 'Adicionada' : 'Adicionar'}</button></td>
                            </tr>`;
                    });
                    table.appendChild(tbody);
                    containerResultadosBusca.appendChild(table);
                } else {
                    containerResultadosBusca.innerHTML = '<p>Nenhuma questão encontrada.</p>';
                }
            });
    });

    // Adicionar/Remover questão
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('btn-adicionar-questao')) {
            const btn = e.target;
            const questaoId = parseInt(btn.dataset.id);
            questoesSelecionadasIds.add(questaoId);
            btn.disabled = true;
            btn.textContent = 'Adicionada';

            listaQuestoesSelecionadas.innerHTML += `
                <li class="list-group-item d-flex justify-content-between align-items-center" id="q-item-${questaoId}">
                    <div><strong>${btn.dataset.assunto}</strong><br><small>${btn.dataset.texto}</small></div>
                    <input type="hidden" name="questoes_ids[]" value="${questaoId}">
                    <button type="button" class="btn btn-sm btn-danger btn-remover-questao" data-id="${questaoId}">&times;</button>
                </li>`;
        }

        if (e.target.classList.contains('btn-remover-questao')) {
            const questaoId = parseInt(e.target.dataset.id);
            questoesSelecionadasIds.delete(questaoId);
            document.getElementById(`q-item-${questaoId}`).remove();

            const btnInModal = document.querySelector(`.btn-adicionar-questao[data-id="${questaoId}"]`);
            if (btnInModal) {
                btnInModal.disabled = false;
                btnInModal.textContent = 'Adicionar';
            }
        }
    });
});
</script>
@endpush