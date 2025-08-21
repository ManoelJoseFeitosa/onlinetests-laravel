@section('title', 'Ver Resultado')
<x-app-layout>
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-0">Resultado: {{ $resultado->avaliacao->nome }}</h2>
                <p class="text-muted">Realizada em: {{ $resultado->data_realizacao->format('d/m/Y às H:i') }}</p>
            </div>
            <a href="{{ route('aluno.resultados.index') }}" class="btn btn-outline-secondary">&larr; Voltar para Meus Resultados</a>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-header bg-primary text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="fs-5">Nota Final</span>
                    <span class="fs-4 fw-bold">{{ $resultado->nota !== null ? number_format($resultado->nota, 2, ',', '.') : 'Aguardando' }}</span>
                </div>
            </div>
        </div>

        @foreach ($resultado->avaliacao->questoes->sortBy('id') as $questao)
            @php $resposta = $respostas_map->get($questao->id); @endphp
            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <strong>Questão {{ $loop->iteration }}</strong>
                    @if ($resposta)
                        @if ($resposta->pontos > 0 && $resposta->pontos < 1) <span class="badge bg-warning text-dark">Parcial</span>
                        @elseif ($resposta->pontos > 0) <span class="badge bg-success">Correta</span>
                        @else <span class="badge bg-danger">Incorreta</span>
                        @endif
                    @else
                        <span class="badge bg-secondary">Não Respondida</span>
                    @endif
                </div>
                <div class="card-body">
                    <p class="card-text">{!! nl2br(e($questao->texto)) !!}</p>
                    @if ($questao->imagem_nome)
                        <img src="{{ asset('storage/uploads/questoes/' . $questao->imagem_nome) }}" class="img-fluid rounded mb-3" style="max-height: 250px;" alt="{{ $questao->imagem_alt }}">
                    @endif
                    <hr>

                    <h6>Sua Resposta:</h6>
                    <div class="p-3 bg-light rounded mb-3">
                        <p class="mb-0"><em>{{ $resposta->resposta_aluno ?? 'Não respondida' }}</em></p>
                    </div>

                    @if ($questao->tipo == 'discursiva')
                        @if ($resposta && $resposta->feedback_professor)
                        <h6>Feedback do Professor:</h6>
                        <div class="p-3 border border-info rounded text-info-emphasis bg-info-subtle">
                            <p class="mb-0">{{ $resposta->feedback_professor }}</p>
                        </div>
                        @endif
                    @else
                        <h6>Gabarito:</h6>
                        <p><strong>{{ $questao->gabarito }}.</strong> {{ $questao['opcao_' . strtolower($questao->gabarito)] }}</p>
                        @if ($questao->justificativa_gabarito)
                        <p><small><strong>Justificativa:</strong> {{ $questao->justificativa_gabarito }}</small></p>
                        @endif
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</x-app-layout>