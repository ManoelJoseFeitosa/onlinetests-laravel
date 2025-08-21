@section('title', 'Gerenciar Acadêmico')
<x-app-layout>
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="h3">Gerenciamento Acadêmico</h1>
            <a href="{{ route('dashboard') }}" class="btn btn-sm btn-secondary">
                <i class="bi bi-arrow-left me-1"></i> Voltar ao Painel
            </a>
        </div>

        @if (session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
        @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
        @endif

        <div class="row">
            <div class="col-lg-5">
                <div class="card shadow mb-4">
                    <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">1. Gerenciar Séries</h6></div>
                    <div class="card-body">
                        <form action="{{ route('coordenador.series.store') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="nome_serie" class="form-label">Nome da Nova Série</label>
                                <input type="text" class="form-control" name="nome" placeholder="Ex: 1° Ano - Ensino Médio" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Salvar Nova Série</button>
                        </form>
                        <hr>
                        <h6 class="text-dark">Séries Cadastradas:</h6>
                        <ul class="list-group">
                            @forelse ($series as $serie)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                {{ $serie->nome }}
                                <div>
                                    <button class="btn btn-sm btn-outline-warning me-1" data-bs-toggle="modal" data-bs-target="#editSerieModal" data-id="{{ $serie->id }}" data-nome="{{ $serie->nome }}">
                                        <i class="bi bi-pencil-fill"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteSerieModal" data-id="{{ $serie->id }}" data-nome="{{ $serie->nome }}">
                                        <i class="bi bi-trash-fill"></i>
                                    </button>
                                </div>
                            </li>
                            @empty
                            <li class="list-group-item text-muted">Nenhuma série cadastrada.</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
                <div class="card shadow mb-4">
                    <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">2. Gerenciar Disciplinas</h6></div>
                    <div class="card-body">
                        <form action="{{ route('coordenador.disciplinas.store') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="nome_disciplina" class="form-label">Nome da Nova Disciplina</label>
                                <input type="text" class="form-control" name="nome" placeholder="Ex: Matemática, Biologia" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Salvar Nova Disciplina</button>
                        </form>
                        <hr>
                        <h6 class="text-dark">Disciplinas Cadastradas:</h6>
                        <ul class="list-group">
                            @forelse ($disciplinas as $disciplina)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                {{ $disciplina->nome }}
                                <div>
                                    <button class="btn btn-sm btn-outline-warning me-1" data-bs-toggle="modal" data-bs-target="#editDisciplinaModal" data-id="{{ $disciplina->id }}" data-nome="{{ $disciplina->nome }}">
                                        <i class="bi bi-pencil-fill"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteDisciplinaModal" data-id="{{ $disciplina->id }}" data-nome="{{ $disciplina->nome }}">
                                        <i class="bi bi-trash-fill"></i>
                                    </button>
                                </div>
                            </li>
                            @empty
                            <li class="list-group-item text-muted">Nenhuma disciplina cadastrada.</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="card shadow mb-4">
                    <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-success">3. Associar Disciplinas às Séries</h6></div>
                    <div class="card-body">
                        <form action="{{ route('coordenador.academico.associar') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="serie_id_associacao" class="form-label">Selecione a Série:</label>
                                <select class="form-select" id="serie_id_associacao" name="serie_id_associacao" required>
                                    <option value="" disabled selected>Escolha uma série para definir as disciplinas</option>
                                    @foreach ($series as $serie)
                                    <option value="{{ $serie->id }}" data-disciplinas-ids="{{ $serie->disciplinas->pluck('id')->join(',') }}">
                                        {{ $serie->nome }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div id="disciplinas-checkboxes" class="mb-3">
                                <p class="text-muted">Selecione uma série acima para ver as disciplinas.</p>
                            </div>
                            <button type="submit" class="btn btn-success w-100">Salvar Associação</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editSerieModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Editar Série</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="editSerieForm" method="POST">
                    @csrf @method('PUT')
                    <div class="modal-body">
                        <label for="edit_nome_serie" class="form-label">Nome da Série</label>
                        <input type="text" class="form-control" id="edit_nome_serie" name="nome_serie" required>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="deleteSerieModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirmar Exclusão</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="deleteSerieForm" method="POST">
                    @csrf @method('DELETE')
                    <div class="modal-body">
                        <p>Você tem certeza que deseja excluir a série <strong id="deleteSerieName"></strong>?</p>
                        <p class="text-danger small">Atenção: Esta ação não pode ser desfeita.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-danger">Excluir</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editDisciplinaModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Editar Disciplina</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="editDisciplinaForm" method="POST">
                    @csrf @method('PUT')
                    <div class="modal-body">
                        <label for="edit_nome_disciplina" class="form-label">Nome da Disciplina</label>
                        <input type="text" class="form-control" id="edit_nome_disciplina" name="nome_disciplina" required>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="deleteDisciplinaModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirmar Exclusão</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="deleteDisciplinaForm" method="POST">
                    @csrf @method('DELETE')
                    <div class="modal-body">
                        <p>Você tem certeza que deseja excluir a disciplina <strong id="deleteDisciplinaName"></strong>?</p>
                        <p class="text-danger small">Atenção: Esta ação não pode ser desfeita.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-danger">Excluir</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const serieSelect = document.getElementById('serie_id_associacao');
        const checkboxesContainer = document.getElementById('disciplinas-checkboxes');
        const todasDisciplinas = @json($disciplinas);

        serieSelect.addEventListener('change', function () {
            const selectedOption = this.options[this.selectedIndex];
            const disciplinasIdsStr = selectedOption.getAttribute('data-disciplinas-ids') || '';
            const disciplinasAssociadas = disciplinasIdsStr.split(',').filter(id => id).map(Number);

            checkboxesContainer.innerHTML = ''; 
            if (!this.value) {
                checkboxesContainer.innerHTML = '<p class="text-muted">Selecione uma série acima para ver as disciplinas.</p>';
                return;
            }

            todasDisciplinas.forEach(disciplina => {
                const isChecked = disciplinasAssociadas.includes(disciplina.id);
                const checkboxHTML = `
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="${disciplina.id}" id="disc_${disciplina.id}" name="disciplinas_selecionadas[]" ${isChecked ? 'checked' : ''}>
                        <label class="form-check-label" for="disc_${disciplina.id}">${disciplina.nome}</label>
                    </div>`;
                checkboxesContainer.innerHTML += checkboxHTML;
            });
        });

        // Lógica para Modais de Edição
        const editSerieModal = document.getElementById('editSerieModal')
        editSerieModal.addEventListener('show.bs.modal', event => {
            const button = event.relatedTarget;
            const serieId = button.dataset.id;
            const serieNome = button.dataset.nome;
            const form = editSerieModal.querySelector('#editSerieForm');
            form.action = `/coordenador/series/${serieId}`; // URL da rota de update
            editSerieModal.querySelector('#edit_nome_serie').value = serieNome;
        });
        const editDisciplinaModal = document.getElementById('editDisciplinaModal');
        editDisciplinaModal.addEventListener('show.bs.modal', event => {
            const button = event.relatedTarget;
            const disciplinaId = button.dataset.id;
            const disciplinaNome = button.dataset.nome;
            const form = editDisciplinaModal.querySelector('#editDisciplinaForm');
            form.action = `/coordenador/disciplinas/${disciplinaId}`;
            editDisciplinaModal.querySelector('#edit_nome_disciplina').value = disciplinaNome;
        });

        // Lógica para Modais de Exclusão
        const deleteSerieModal = document.getElementById('deleteSerieModal');
        deleteSerieModal.addEventListener('show.bs.modal', event => {
            const button = event.relatedTarget;
            const serieId = button.dataset.id;
            const serieNome = button.dataset.nome;
            const form = deleteSerieModal.querySelector('#deleteSerieForm');
            form.action = `/coordenador/series/${serieId}`;
            deleteSerieModal.querySelector('#deleteSerieName').textContent = serieNome;
        });
        const deleteDisciplinaModal = document.getElementById('deleteDisciplinaModal');
        deleteDisciplinaModal.addEventListener('show.bs.modal', event => {
            const button = event.relatedTarget;
            const disciplinaId = button.dataset.id;
            const disciplinaNome = button.dataset.nome;
            const form = deleteDisciplinaModal.querySelector('#deleteDisciplinaForm');
            form.action = `/coordenador/disciplinas/${disciplinaId}`;
            deleteDisciplinaModal.querySelector('#deleteDisciplinaName').textContent = disciplinaNome;
        });
    });
    </script>
    @endpush
</x-app-layout>