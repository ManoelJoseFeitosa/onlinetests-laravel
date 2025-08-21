@section('title', 'Painel de Auditoria')
<x-app-layout>
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0 text-gray-800">Painel de Auditoria</h1>
            <a href="{{ route('dashboard') }}" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>Voltar ao Painel
            </a>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 fw-bold text-primary">Filtrar Registros</h6>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('coordenador.auditoria.index') }}" class="row g-3 align-items-end">
                    <div class="col-md-5">
                        <label for="q" class="form-label">Buscar por Email ou IP</label>
                        <input type="text" id="q" name="q" class="form-control" value="{{ $search_query }}" placeholder="parte.do.email@exemplo.com ou 192.168.1.1">
                    </div>
                    <div class="col-md-5">
                        <label for="action_filter" class="form-label">Ação</label>
                        <select id="action_filter" name="action_filter" class="form-select">
                            <option value="">-- Todas as Ações --</option>
                            @foreach ($unique_actions as $act)
                                <option value="{{ $act }}" @if($act == $action_filter) selected @endif>{{ Str::of($act)->replace('_', ' ')->title() }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">Filtrar</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Data/Hora</th>
                                <th>Usuário</th>
                                <th>Ação</th>
                                <th>Alvo</th>
                                <th>Endereço IP</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($logs as $log)
                            <tr>
                                <td>{{ $log->timestamp->format('d/m/Y H:i:s') }}</td>
                                <td>{{ $log->user_email }}</td>
                                <td><span class="badge bg-info text-dark">{{ Str::of($log->action)->replace('_', ' ')->title() }}</span></td>
                                <td>{{ $log->target_type ?? 'N/A' }} #{{ $log->target_id ?? '' }}</td>
                                <td><small class="text-muted">{{ $log->ip_address ?? 'N/A' }}</small></td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <i class="bi bi-journal-x fs-1 text-muted"></i>
                                    <p class="mb-0 mt-2">Nenhum registro de auditoria encontrado.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($logs->hasPages())
                    <nav class="mt-4">
                        {{ $logs->links() }}
                    </nav>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>