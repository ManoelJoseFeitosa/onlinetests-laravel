@section('title', 'Painel do Administrador')
<x-app-layout>
    <div class="container">
        {{-- Adicionando a mensagem de sucesso que virá do Controller --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">Painel do Super Administrador</h2>
            <a href="{{ route('superadmin.escolas.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle-fill me-2"></i>Nova Escola
            </a>
        </div>

        <div class="card shadow-sm">
            <div class="card-header">
                <h5 class="mb-0">Escolas Cadastradas</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Nome da Escola</th>
                                <th>CNPJ</th>
                                <th class="text-center">Nº de Alunos</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Plano</th>
                                <th class="text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($escolas as $escola)
                            <tr>
                                <td><strong>{{ $escola->nome }}</strong></td>
                                <td>{{ $escola->cnpj ?? 'Não informado' }}</td>
                                <td class="text-center">{{ $escola->alunos_count }}</td>
                                <td class="text-center">
                                    <span class="badge bg-{{ $escola->status == 'ativo' ? 'success' : 'danger' }}">
                                        {{ ucfirst($escola->status) }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-info text-dark">
                                        {{ ucfirst($escola->plano) }}
                                    </span>
                                </td>
                                
                                {{-- ## CÓDIGO CORRIGIDO AQUI ## --}}
                                <td class="text-center">
                                    <a href="{{ route('superadmin.escolas.edit', $escola) }}" class="btn btn-sm btn-outline-primary me-2" title="Editar Escola">
                                        <i class="bi bi-pencil-fill"></i> Editar
                                    </a>
                                    <form action="{{ route('superadmin.escolas.toggleStatus', $escola) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm {{ $escola->status == 'ativo' ? 'btn-outline-warning' : 'btn-outline-success' }}" title="{{ $escola->status == 'ativo' ? 'Bloquear' : 'Ativar' }}">
                                            @if ($escola->status == 'ativo')
                                                <i class="bi bi-lock-fill"></i> Bloquear
                                            @else
                                                <i class="bi bi-unlock-fill"></i> Ativar
                                            @endif
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">Nenhuma escola cadastrada.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>