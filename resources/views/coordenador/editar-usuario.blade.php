@section('title', 'Painel Editar Usuário')
<x-app-layout>
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">Editar Usuário: <span class="text-primary">{{ $usuario->nome }}</span></h1>
            <a href="{{ route('coordenador.usuarios.index') }}" class="btn btn-outline-secondary">&larr; Voltar para Gerenciamento</a>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="card shadow-sm">
            <div class="card-body p-4 p-md-5">
                
                {{-- Formulário de Dados Pessoais e Associações de Professor --}}
                <form method="POST" action="{{ route('coordenador.usuarios.update', $usuario) }}" id="form-editar-usuario">
                    @csrf
                    @method('PUT')
                    <h5 class="mb-3 border-bottom pb-2">Dados Pessoais</h5>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="nome" class="form-label">Nome Completo</label>
                            <input type="text" class="form-control" id="nome" name="nome" value="{{ old('nome', $usuario->nome) }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">E-mail</label>
                            <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $usuario->email) }}" required>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label for="senha" class="form-label">Nova Senha</label>
                        <input type="password" class="form-control" id="senha" name="senha" placeholder="Deixe em branco para não alterar">
                        <small class="form-text text-muted">A senha atual não será exibida. Digite uma nova senha apenas se desejar alterá-la.</small>
                    </div>

                    <hr>

                    <h5 class="mb-3 border-bottom pb-2">Perfil e Associações</h5>
                    <div class="mb-3">
                        <label for="role_display" class="form-label">Função</label>
                        <input type="text" class="form-control" id="role_display" value="{{ ucfirst($usuario->role) }}" readonly disabled>
                        <small class="form-text text-muted">A função de um usuário não pode ser alterada após a criação.</small>
                    </div>
                    
                    {{-- Campos que só aparecem para Professores --}}
                    @if ($usuario->role == 'professor')
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="disciplinas_ids_edit" class="form-label">Disciplinas Lecionadas</label>
                                <select class="form-select" name="disciplinas_ids[]" id="disciplinas_ids_edit" multiple size="6">
                                    @foreach ($disciplinas as $disciplina)
                                        <option value="{{ $disciplina->id }}" @if($usuario->disciplinasLecionadas->contains($disciplina->id)) selected @endif>
                                            {{ $disciplina->nome }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="form-text">Segure Ctrl (ou Cmd no Mac) para selecionar.</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="series_ids_edit" class="form-label">Turmas Lecionadas</label>
                                <select class="form-select" name="series_ids[]" id="series_ids_edit" multiple size="6">
                                    @foreach ($series as $serie)
                                        <option value="{{ $serie->id }}" @if($usuario->seriesLecionadas->contains($serie->id)) selected @endif>
                                            {{ $serie->nome }}
                                        </option>
                                    @endforeach
                                </select>
                                 <div class="form-text">Segure Ctrl (ou Cmd no Mac) para selecionar.</div>
                            </div>
                        </div>
                    @endif
                </form>
                
                {{-- Seção de Matrícula (só aparece para alunos) --}}
                @if ($usuario->role == 'aluno')
                <fieldset class="border p-3 rounded mt-4">
                    <legend class="fs-6 fw-bold px-2">Gerenciar Matrícula por Ano Letivo</legend>

                    <div class="mb-3">
                        <label for="ano_letivo_select" class="form-label">Selecione o Ano Letivo para visualizar ou editar a matrícula:</label>
                        <select id="ano_letivo_select" class="form-select">
                            <option value="">-- Selecione um Ano --</option>
                            @foreach ($anos_letivos as $ano)
                            <option value="{{ $ano->id }}" @if($ano->status == 'ativo') selected @endif>
                                {{ $ano->ano }} ({{ ucfirst($ano->status) }})
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div id="matricula-form-container" class="bg-light p-3 rounded" style="min-height: 150px;">
                        {{-- O JavaScript vai carregar o formulário aqui --}}
                    </div>
                </fieldset>
                @endif
                
                <div class="text-center mt-5">
                    <button type="submit" form="form-editar-usuario" class="btn btn-primary btn-lg">
                        <i class="bi bi-check-circle-fill me-2"></i>Salvar Alterações Cadastrais
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        if (document.getElementById('ano_letivo_select')) {
            const anoLetivoSelect = document.getElementById('ano_letivo_select');
            const matriculaFormContainer = document.getElementById('matricula-form-container');
            const userId = '{{ $usuario->id }}';
            const allSeries = @json($series->map(fn($s) => ['id' => $s->id, 'nome' => $s->nome]));

            function carregarFormMatricula() {
                const anoId = anoLetivoSelect.value;
                if (!anoId) {
                    matriculaFormContainer.innerHTML = '<p class="text-center text-muted m-0 p-5">Selecione um ano letivo acima.</p>';
                    return;
                }

                matriculaFormContainer.innerHTML = '<p class="text-muted text-center p-5">Carregando...</p>';
                
                // Usando a rota nomeada do Laravel
                const url = "{{ route('coordenador.api.matricula.show', ['user' => $usuario, 'anoLetivo' => ':anoId']) }}".replace(':anoId', anoId);

                fetch(url)
                    .then(response => response.json())
                    .then(data => {
                        let seriesOptionsHTML = allSeries.map(serie => {
                            const isSelected = data.matriculado && data.serie_id === serie.id ? 'selected' : '';
                            return `<option value="${serie.id}" ${isSelected}>${serie.nome}</option>`;
                        }).join('');
                        
                        const statusText = data.status.charAt(0).toUpperCase() + data.status.slice(1);
                        
                        const formHTML = `
                            <form action="{{ route('coordenador.matricula.store') }}" method="POST">
                                @csrf
                                <input type="hidden" name="user_id" value="${userId}">
                                <input type="hidden" name="ano_letivo_id" value="${anoId}">
                                <div class="mb-3">
                                    <label for="serie_matricula" class="form-label">Série para o ano de <strong>${anoLetivoSelect.options[anoLetivoSelect.selectedIndex].text.match(/\\d{4}/)[0]}</strong>:</label>
                                    <select id="serie_matricula" name="serie_id" class="form-select" required>
                                        <option value="">-- Selecione a série --</option>
                                        ${seriesOptionsHTML}
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-success">
                                    <i class="bi bi-check-circle me-1"></i>
                                    ${data.matriculado ? 'Atualizar Matrícula' : 'Matricular Aluno'}
                                </button>
                            </form>
                        `;
                        matriculaFormContainer.innerHTML = formHTML;
                    })
                    .catch(error => {
                        matriculaFormContainer.innerHTML = '<p class="text-danger fw-bold">Erro ao carregar dados.</p>';
                    });
            }

            anoLetivoSelect.addEventListener('change', carregarFormMatricula);
            if (anoLetivoSelect.value) {
                carregarFormMatricula();
            }
        }
    });
    </script>
    @endpush
</x-app-layout>