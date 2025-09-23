<x-app-layout>
    <x-slot name="title">Gerenciar Notas</x-slot>

    <div class="container mt-4">
        <h2 class="mb-4">Gerenciar Notas da Turma</h2>

        <div class="card shadow-sm">
            <div class="card-body">
                <div class="mb-3">
                    <label for="serie_id" class="form-label fw-bold">Selecione uma Turma:</label>
                    <select id="serie_id" class="form-select">
                        <option value="">-- Selecione uma turma para começar --</option>
                        @foreach($series as $serie)
                            <option value="{{ $serie->id }}">{{ $serie->nome }}</option>
                        @endforeach
                    </select>
                </div>

                <div id="boletim-container" class="table-responsive" style="display: none;">
                    <table class="table table-bordered table-hover">
                        <thead id="boletim-head"></thead>
                        <tbody id="boletim-body"></tbody>
                    </table>
                </div>
                <div id="loading" style="display: none;" class="text-center">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Carregando...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editNoteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Alterar Nota</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p><strong>Aluno:</strong> <span id="modal-aluno-nome"></span></p>
                    <p><strong>Avaliação:</strong> <span id="modal-avaliacao-nome"></span></p>
                    <input type="hidden" id="modal-resultado-id">
                    <div class="mb-3">
                        <label for="modal-nova-nota" class="form-label">Nova Nota (0 a 10)</label>
                        <input type="number" step="0.01" min="0" max="10" class="form-control" id="modal-nova-nota">
                    </div>
                    <div class="mb-3">
                        <label for="modal-justificativa" class="form-label">Justificativa (Opcional)</label>
                        <textarea class="form-control" id="modal-justificativa" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="saveNoteButton">Salvar Nota</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const serieSelect = document.getElementById('serie_id');
        const container = document.getElementById('boletim-container');
        const thead = document.getElementById('boletim-head');
        const tbody = document.getElementById('boletim-body');
        const loading = document.getElementById('loading');
        
        const editModal = new bootstrap.Modal(document.getElementById('editNoteModal'));
        const modalAluno = document.getElementById('modal-aluno-nome');
        const modalAvaliacao = document.getElementById('modal-avaliacao-nome');
        const modalResultadoId = document.getElementById('modal-resultado-id');
        const modalNovaNota = document.getElementById('modal-nova-nota');
        const modalJustificativa = document.getElementById('modal-justificativa');
        const saveButton = document.getElementById('saveNoteButton');

        serieSelect.addEventListener('change', function() {
            const serieId = this.value;
            if (!serieId) {
                container.style.display = 'none';
                return;
            }

            loading.style.display = 'block';
            container.style.display = 'none';

            fetch(`/professor/turmas/${serieId}/dados-boletim`)
                .then(response => response.json())
                .then(data => {
                    thead.innerHTML = '';
                    tbody.innerHTML = '';

                    if (data.avaliacoes.length === 0) {
                        loading.style.display = 'none';
                        container.style.display = 'block';
                        tbody.innerHTML = '<tr><td class="text-center text-muted py-4">Nenhuma avaliação foi respondida por esta turma ainda.</td></tr>';
                        return;
                    }

                    let headerRow = '<tr><th>Aluno</th>';
                    data.avaliacoes.forEach(av => headerRow += `<th>${av.nome}</th>`);
                    headerRow += '</tr>';
                    thead.innerHTML = headerRow;

                    data.alunos.forEach(aluno => {
                        let bodyRow = `<tr><td>${aluno.nome}</td>`;
                        data.avaliacoes.forEach(av => {
                            const key = `${aluno.id}-${av.id}`;
                            const resultado = data.resultados[key];
                            if (resultado) {
                                const notaFormatada = parseFloat(resultado.nota).toLocaleString('pt-BR', { minimumFractionDigits: 2 });
                                bodyRow += `<td class="text-center nota-editavel" style="cursor: pointer;"
                                                data-resultado-id="${resultado.id}" 
                                                data-aluno-nome="${aluno.nome}"
                                                data-avaliacao-nome="${av.nome}">${notaFormatada}</td>`;
                            } else {
                                bodyRow += '<td class="text-center">-</td>';
                            }
                        });
                        bodyRow += '</tr>';
                        tbody.innerHTML += bodyRow;
                    });
                    
                    loading.style.display = 'none';
                    container.style.display = 'block';
                });
        });

        tbody.addEventListener('click', function(e) {
            if (e.target && e.target.classList.contains('nota-editavel')) {
                const cell = e.target;
                modalAluno.textContent = cell.dataset.alunoNome;
                modalAvaliacao.textContent = cell.dataset.avaliacaoNome;
                modalResultadoId.value = cell.dataset.resultadoId;
                modalNovaNota.value = parseFloat(cell.textContent.replace(',', '.')).toFixed(2);
                modalJustificativa.value = '';
                editModal.show();
            }
        });

        saveButton.addEventListener('click', function() {
            const resultadoId = modalResultadoId.value;
            const novaNota = modalNovaNota.value;
            const justificativa = modalJustificativa.value;

            // ### CORREÇÃO APLICADA AQUI ###
            // A URL para salvar a nota estava incorreta.
            fetch(`/professor/resultados/${resultadoId}/atualizar-nota`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    nova_nota: novaNota,
                    justificativa: justificativa
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const cellToUpdate = document.querySelector(`td[data-resultado-id='${resultadoId}']`);
                    if(cellToUpdate) {
                        cellToUpdate.textContent = data.nova_nota_formatada;
                        cellToUpdate.style.backgroundColor = '#d1e7dd';
                        setTimeout(() => { cellToUpdate.style.backgroundColor = ''; }, 1500);
                    }
                    editModal.hide();
                } else {
                    alert('Erro ao atualizar a nota. Verifique os valores e tente novamente.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Ocorreu um erro de comunicação. Tente novamente.');
            });
        });
    });
    </script>
    @endpush
</x-app-layout>
