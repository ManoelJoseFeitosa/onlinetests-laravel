<x-app-layout>
    <x-slot name="title">
        Gerenciar Usuários
    </x-slot>

    <div class="container">
        {{-- Título e Botão Voltar --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h1 class="display-6">Gerenciamento de Usuários 🧑‍🏫</h1>
                <p class="lead mb-0">Cadastre novos usuários no sistema ou edite os existentes na lista abaixo.</p>
            </div>
            <div>
                <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">&larr; Voltar ao Painel</a>
            </div>
        </div>
        <hr class="mb-5">

        {{-- Alertas --}}
        @if (session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
        @if (session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif
        @if ($errors->any())
        <div class="alert alert-danger">
            <h6>Ocorreu um erro:</h6>
            <ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
        @endif

        {{-- Formulário de Cadastro --}}
        <div class="row g-5 justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white"><h5 class="mb-0"><i class="bi bi-person-plus-fill me-2"></i>Cadastrar Novo Usuário</h5></div>
                    <div class="card-body">
                        {{-- O formulário que já fizemos continua aqui... --}}
                        <form method="POST" action="{{ route('coordenador.usuarios.store') }}">
                            @csrf
                            <div class="row g-3">
                                <div class="col-md-12"><label for="nome" class="form-label">Nome Completo</label><input type="text" class="form-control" id="nome" name="nome" value="{{ old('nome') }}" required></div>
                                <div class="col-md-6"><label for="email" class="form-label">Email</label><input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required></div>
                                <div class="col-md-6"><label for="senha" class="form-label">Senha Provisória</label><input type="password" class="form-control" id="senha" name="senha" required placeholder="Defina uma senha inicial"></div>
                                <div class="col-md-12"><label for="role" class="form-label">Perfil do Usuário</label><select class="form-select" id="role" name="role" required><option selected disabled value="">Selecione um perfil...</option><option value="aluno" @if(old('role') == 'aluno') selected @endif>Aluno</option><option value="professor" @if(old('role') == 'professor') selected @endif>Professor</option><option value="coordenador" @if(old('role') == 'coordenador') selected @endif>Coordenador</option></select></div>
                                <div class="col-md-12" id="campo_serie" style="display: none;"><label for="serie_id" class="form-label">Série</label><select class="form-select" id="serie_id" name="serie_id"><option value="">Selecione a série do aluno...</option>@foreach ($series as $serie)<option value="{{ $serie->id }}" @if(old('serie_id') == $serie->id) selected @endif>{{ $serie->nome }}</option>@endforeach</select></div>
                                <div class="col-md-12" id="campo_disciplinas" style="display: none;"><label for="disciplinas_ids" class="form-label">Disciplinas Lecionadas</label><select class="form-select" id="disciplinas_ids" name="disciplinas_ids[]" multiple size="4">@foreach ($disciplinas as $disciplina)<option value="{{ $disciplina->id }}">{{ $disciplina->nome }}</option>@endforeach</select><div class="form-text">Segure Ctrl (ou Cmd no Mac) para selecionar.</div></div>
                                <div class="col-md-12" id="campo_series_professor" style="display: none;"><label for="series_ids" class="form-label">Turmas Lecionadas</label><select class="form-select" id="series_ids" name="series_ids[]" multiple size="4">@foreach ($series as $serie)<option value="{{ $serie->id }}">{{ $serie->nome }}</option>@endforeach</select><div class="form-text">Segure Ctrl (ou Cmd no Mac) para selecionar.</div></div>
                            </div>
                            <hr class="my-4">
                            <button type="submit" class="btn btn-primary w-100 btn-lg"><i class="bi bi-check-circle-fill me-2"></i>Criar Usuário</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <hr class="my-5">
        <h2 class="display-6 mb-4"><i class="bi bi-people-fill me-2"></i>Usuários Cadastrados</h2>

        <ul class="nav nav-tabs nav-fill mb-3" id="userTab" role="tablist">
            <li class="nav-item" role="presentation"><button class="nav-link active" id="alunos-tab" data-bs-toggle="tab" data-bs-target="#alunos-pane" type="button"><i class="bi bi-backpack-fill me-1"></i> Alunos</button></li>
            <li class="nav-item" role="presentation"><button class="nav-link" id="professores-tab" data-bs-toggle="tab" data-bs-target="#professores-pane" type="button"><i class="bi bi-person-video3 me-1"></i> Professores</button></li>
            <li class="nav-item" role="presentation"><button class="nav-link" id="coordenadores-tab" data-bs-toggle="tab" data-bs-target="#coordenadores-pane" type="button"><i class="bi bi-person-workspace me-1"></i> Coordenadores</button></li>
        </ul>

        <div class="tab-content" id="userTabContent">
            
            <div class="tab-pane fade show active" id="alunos-pane" role="tabpanel">
                <div class="table-responsive"><table class="table table-striped table-hover align-middle">
                    <thead><tr><th>Nome</th><th>Email</th><th class="text-center">Perfil</th><th>Série Atual</th><th class="text-center">Ações</th></tr></thead>
                    <tbody>
                        @forelse ($usuarios->where('role', 'aluno') as $usuario)
                            <tr>
                                <td>{{ $usuario->nome }}</td>
                                <td>{{ $usuario->email }}</td>
                                <td class="text-center"><span class="badge bg-secondary">{{ ucfirst($usuario->role) }}</span></td>
                                <td>{!! $usuario->matriculaAtiva()?->serie->nome ?? '<span class="text-danger small">Sem matrícula ativa</span>' !!}</td>
                                <td class="text-center"><a href="{{ route('coordenador.usuarios.edit', $usuario) }}" class="btn btn-sm btn-outline-primary" title="Editar {{ $usuario->nome }}"><i class="bi bi-pencil-fill"></i> Editar</a></td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center text-muted py-4">Nenhum aluno cadastrado.</td></tr>
                        @endforelse
                    </tbody>
                </table></div>
            </div>

            <div class="tab-pane fade" id="professores-pane" role="tabpanel">
                <div class="table-responsive"><table class="table table-striped table-hover align-middle">
                    <thead><tr><th>Nome</th><th>Email</th><th class="text-center">Perfil</th><th>Turmas Lecionadas</th><th class="text-center">Ações</th></tr></thead>
                    <tbody>
                        @forelse ($usuarios->where('role', 'professor') as $usuario)
                            <tr>
                                <td>{{ $usuario->nome }}</td>
                                <td>{{ $usuario->email }}</td>
                                <td class="text-center"><span class="badge bg-info text-dark">{{ ucfirst($usuario->role) }}</span></td>
                                <td>
                                    @forelse ($usuario->seriesLecionadas as $serie)
                                        <span class="badge bg-secondary fw-normal me-1">{{ $serie->nome }}</span>
                                    @empty
                                        <span class="text-muted small">Nenhuma turma associada</span>
                                    @endforelse
                                </td>
                                <td class="text-center"><a href="{{ route('coordenador.usuarios.edit', $usuario) }}" class="btn btn-sm btn-outline-primary" title="Editar {{ $usuario->nome }}"><i class="bi bi-pencil-fill"></i> Editar</a></td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center text-muted py-4">Nenhum professor cadastrado.</td></tr>
                        @endforelse
                    </tbody>
                </table></div>
            </div>
            
            <div class="tab-pane fade" id="coordenadores-pane" role="tabpanel">
                 <div class="table-responsive"><table class="table table-striped table-hover align-middle">
                    <thead><tr><th>Nome</th><th>Email</th><th class="text-center">Perfil</th><th class="text-center">Ações</th></tr></thead>
                    <tbody>
                        @forelse ($usuarios->where('role', 'coordenador') as $usuario)
                            <tr>
                                <td>{{ $usuario->nome }}</td>
                                <td>{{ $usuario->email }}</td>
                                <td class="text-center"><span class="badge bg-primary">{{ ucfirst($usuario->role) }}</span></td>
                                <td class="text-center"><a href="{{ route('coordenador.usuarios.edit', $usuario) }}" class="btn btn-sm btn-outline-primary" title="Editar {{ $usuario->nome }}"><i class="bi bi-pencil-fill"></i> Editar</a></td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center text-muted py-4">Nenhum coordenador cadastrado.</td></tr>
                        @endforelse
                    </tbody>
                </table></div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const roleSelect = document.getElementById('role');
        if (!roleSelect) return;
        const campoSerie = document.getElementById('campo_serie');
        const campoDisciplinas = document.getElementById('campo_disciplinas');
        const campoSeriesProfessor = document.getElementById('campo_series_professor');
        const selectSerie = document.getElementById('serie_id');
        function toggleFields() {
            campoSerie.style.display = 'none'; campoDisciplinas.style.display = 'none';
            campoSeriesProfessor.style.display = 'none'; selectSerie.required = false;
            if (roleSelect.value === 'aluno') {
                campoSerie.style.display = 'block'; selectSerie.required = true;
            } else if (roleSelect.value === 'professor') {
                campoDisciplinas.style.display = 'block'; campoSeriesProfessor.style.display = 'block';
            }
        }
        roleSelect.addEventListener('change', toggleFields);
        toggleFields(); 
    });
    </script>
    @endpush
</x-app-layout>