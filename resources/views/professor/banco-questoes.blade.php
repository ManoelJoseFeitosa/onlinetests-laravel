<x-app-layout>
    {{-- A forma correta de definir o título usando um slot --}}
    <x-slot name="title">
        Meu Banco de Questões
    </x-slot>

    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">Meu Banco de Questões</h2>
            <div>
                <a href="{{ route('professor.banco-questoes.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle-fill me-2"></i>Adicionar Nova Questão
                </a>
                <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">&larr; Voltar ao Painel</a>
            </div>
        </div>
        <p class="text-muted">Visualize, edite ou remova as questões que você criou.</p>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="card shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Disciplina</th>
                                <th>Série(s)</th>  {{-- Título da coluna atualizado --}}
                                <th>Assunto</th>
                                <th>Enunciado</th>
                                <th class="text-center">Nível</th>
                                <th class="text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($questoes as $questao)
                            <tr>
                                <td>{{ $questao->id }}</td>
                                <td>{{ $questao->disciplina->nome }}</td>
                                <td>
                                    {{-- CORREÇÃO APLICADA AQUI --}}
                                    {{-- Loop para exibir todas as séries associadas --}}
                                    @foreach($questao->series as $serie)
                                        <span class="badge bg-secondary fw-normal">{{ $serie->nome }}</span>
                                    @endforeach
                                </td>
                                <td>{{ $questao->assunto }}</td>
                                <td>{{ Str::limit($questao->texto, 80) }}</td>
                                <td class="text-center">
                                    @php
                                        $levelClass = match($questao->nivel) {
                                            'facil' => 'bg-success',
                                            'media' => 'bg-primary',
                                            'dificil' => 'bg-danger',
                                            default => 'bg-secondary',
                                        };
                                    @endphp
                                    <span class="badge {{ $levelClass }}">{{ ucfirst($questao->nivel) }}</span>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('professor.banco-questoes.edit', $questao) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-pencil-fill"></i> Editar
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">Você ainda não criou nenhuma questão.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($questoes->hasPages())
                <div class="mt-4 d-flex justify-content-center">
                    {{ $questoes->links('pagination::bootstrap-5') }}
                </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>