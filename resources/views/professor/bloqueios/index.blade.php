<x-app-layout>
    <x-slot name="title">Avaliações Bloqueadas</x-slot>

    <div class="container">
        <h1 class="h3 mb-3">Avaliações Bloqueadas</h1>
        <p class="text-muted">
            Aqui estão listadas as avaliações que foram bloqueadas porque o aluno tentou sair da tela de prova.
        </p>

        <div class="card shadow-sm">
            <div class="card-body">
                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                @if ($resultados_bloqueados->isEmpty())
                    <div class="alert alert-info text-center">
                        <i class="bi bi-check-circle fs-3 d-block mb-2"></i>
                        Nenhuma avaliação bloqueada no momento.
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Aluno</th>
                                    <th>Avaliação</th>
                                    <th>Data</th>
                                    <th class="text-center">Ação</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($resultados_bloqueados as $resultado)
                                    <tr>
                                        <td>{{ $resultado->aluno->nome ?? 'Aluno não encontrado' }}</td>
                                        <td>{{ $resultado->avaliacao->nome ?? 'Avaliação não encontrada' }}</td>
                                        <td>{{ $resultado->updated_at->format('d/m/Y H:i') }}</td>
                                        <td class="text-center">
                                            <form action="{{ route('professor.bloqueios.desbloquear', $resultado) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja desbloquear esta avaliação?');">
                                                @csrf
                                                <button type="submit" class="btn btn-warning btn-sm">
                                                    <i class="bi bi-unlock-fill me-1"></i> Desbloquear
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
