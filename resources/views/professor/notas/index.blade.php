<x-app-layout>
    <x-slot name="title">Gerenciar Notas</x-slot>

    <div class="container mt-4">
        <h2 class="mb-4">Gerenciar Notas da Turma</h2>

        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="card shadow-sm">
            <div class="card-body">
                <div class="mb-3">
                    <label for="serie_id" class="form-label fw-bold">Selecione uma Turma:</label>
                    <select id="serie_id" class="form-select">
                        <option value="">-- Selecione uma turma para começar --</option>
                        @foreach($series as $serie)
                            <option value="{{ $serie->id }}" @if($serieSelecionada && $serieSelecionada->id == $serie->id) selected @endif>
                                {{ $serie->nome }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- A tabela agora é renderizada diretamente com PHP --}}
                @if($serieSelecionada)
                    @if($alunos->isNotEmpty() && $avaliacoes->isNotEmpty())
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Aluno</th>
                                        @foreach($avaliacoes as $avaliacao)
                                            <th>{{ $avaliacao->nome }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($alunos as $aluno)
                                        <tr>
                                            <td>{{ $aluno->nome }}</td>
                                            @foreach($avaliacoes as $avaliacao)
                                                @php
                                                    $key = $aluno->id . '-' . $avaliacao->id;
                                                    $resultado = $resultadosMap[$key] ?? null;
                                                @endphp
                                                @if($resultado)
                                                    <td class="text-center nota-editavel" style="cursor: pointer;"
                                                        data-resultado-id="{{ $resultado->id }}" 
                                                        data-aluno-nome="{{ $aluno->nome }}"
                                                        data-avaliacao-nome="{{ $avaliacao->nome }}">
                                                        {{ number_format($resultado->nota, 2, ',', '.') }}
                                                    </td>
                                                @else
                                                    <td class="text-center">-</td>
                                                @endif
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info text-center">
                            Nenhum aluno ou avaliação com resultados encontrados para esta turma.
                        </div>
                    @endif
                @endif

            </div>
        </div>
    </div>

    {{-- O Modal de edição continua o mesmo --}}
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
        
        // Novo script: Recarregar a página com o ID da série na URL
        serieSelect.addEventListener('change', function() {
            const serieId = this.value;
            if (serieId) {
                // Monta a nova URL e redireciona
                window.location.href = `{{ route('professor.notas.index') }}?serie_id=${serieId}`;
            } else {
                 window.location.href = `{{ route('professor.notas.index') }}`;
            }
        });

        // O script para o modal de edição continua o mesmo, pois é independente
        const editModal = new bootstrap.Modal(document.getElementById('editNoteModal'));
        const modalAluno = document.getElementById('modal-aluno-nome');
        const modalAvaliacao = document.getElementById('modal-avaliacao-nome');
        const modalResultadoId = document.getElementById('modal-resultado-id');
        const modalNovaNota = document.getElementById('modal-nova-nota');
        const modalJustificativa = document.getElementById('modal-justificativa');
        const saveButton = document.getElementById('saveNoteButton');
        const tbody = document.querySelector('.table tbody');

        if (tbody) {
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
        }

        saveButton.addEventListener('click', function() {
            const resultadoId = modalResultadoId.value;
            const novaNota = modalNovaNota.value;
            const justificativa = modalJustificativa.value;

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
