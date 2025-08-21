<x-app-layout>
    <x-slot name="title">
        Corrigindo Avaliação
    </x-slot>

    <div class="container">
        <h2 class="h3 mb-3">Corrigindo Avaliação</h2>

        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <strong>Aluno:</strong> {{ $resultado->aluno->nome }} <br>
                <strong>Avaliação:</strong> {{ $resultado->avaliacao->nome }}
            </div>
        </div>

        <form action="{{ route('coordenador.correcao.store', $resultado) }}" method="POST">
            @csrf
            @foreach($resultado->avaliacao->questoes as $questao)
                @php
                    $resposta = $respostas_map->get($questao->id);
                @endphp
                <div class="card shadow-sm mb-3">
                    <div class="card-header">
                        <strong>Questão {{ $loop->iteration }}:</strong>
                    </div>
                    <div class="card-body">
                        <p class="card-text">{!! nl2br(e($questao->texto)) !!}</p>
                        <hr>
                        <h6 class="text-primary">Resposta do Aluno:</h6>
                        <div class="p-3 bg-light rounded mb-3">
                            <p class="mb-0 fst-italic">
                                {{ $resposta->resposta_aluno ?? 'Não respondida' }}
                            </p>
                        </div>

                        <div class="border p-3 rounded">
                            @if($questao->tipo == 'discursiva')
                                <h6 class="text-success">Avaliar Resposta Discursiva:</h6>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="correcoes[{{ $questao->id }}][status]" id="correta_{{ $questao->id }}" value="correta" @if(old('correcoes.'.$questao->id.'.status', $resposta->status_correcao ?? '') == 'correta') checked @endif required>
                                    <label class="form-check-label" for="correta_{{ $questao->id }}">Correta (Nota máxima)</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="correcoes[{{ $questao->id }}][status]" id="parcial_{{ $questao->id }}" value="parcial" @if(old('correcoes.'.$questao->id.'.status', $resposta->status_correcao ?? '') == 'parcial') checked @endif>
                                    <label class="form-check-label" for="parcial_{{ $questao->id }}">Parcialmente Correta (Metade da nota)</label>
                                </div>
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="radio" name="correcoes[{{ $questao->id }}][status]" id="incorreta_{{ $questao->id }}" value="incorreta" @if(old('correcoes.'.$questao->id.'.status', $resposta->status_correcao ?? 'incorreta') == 'incorreta') checked @endif>
                                    <label class="form-check-label" for="incorreta_{{ $questao->id }}">Incorreta (Zero)</label>
                                </div>
                            @else
                                <div class="alert alert-info py-2">
                                    <i class="bi bi-robot me-2"></i>A pontuação desta questão objetiva é calculada automaticamente.
                                </div>
                            @endif

                            {{-- Campo de Feedback (agora para TODAS as questões) --}}
                            <div class="mt-3">
                                <label for="feedback_{{ $questao->id }}" class="form-label fw-bold">Feedback para o Aluno (Opcional):</label>
                                <textarea name="correcoes[{{ $questao->id }}][feedback]" id="feedback_{{ $questao->id }}" class="form-control" rows="2" placeholder="Digite um comentário sobre a resposta do aluno...">{{ old('correcoes.'.$questao->id.'.feedback', $resposta->feedback_professor ?? '') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach

            <div class="text-center my-4">
                <a href="{{ route('coordenador.modelos.show', $resultado->avaliacao->modelo_id) }}" class="btn btn-secondary">Cancelar</a>
                <button type="submit" class="btn btn-success btn-lg">
                    <i class="bi bi-check-circle-fill me-2"></i>Finalizar Correção
                </button>
            </div>
        </form>
    </div>
</x-app-layout>