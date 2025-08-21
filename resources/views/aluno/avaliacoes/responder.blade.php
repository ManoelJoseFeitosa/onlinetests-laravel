<x-app-layout>
    @push('styles')
    <style>
        #timer-fixed-container { position: fixed; top: 80px; right: 20px; padding: 8px 15px; background-color: rgba(0, 0, 0, 0.7); color: white; border-radius: 0.5rem; box-shadow: 0 4px 8px rgba(0,0,0,0.1); z-index: 1050; font-size: 1.25rem; font-weight: 500; transition: background-color 0.5s, color 0.5s; }
        #timer-fixed-container.timer-alerta { background-color: #ffc107; color: #333; font-weight: bold; }
        #timer-fixed-container.timer-critico { background-color: #dc3545; color: white; font-weight: bold; }
        #flash-message { position: fixed; top: 0; left: 50%; transform: translateX(-50%); background-color: #ffc107; color: #333; padding: 10px 20px; border-radius: 0 0 5px 5px; box-shadow: 0 4px 8px rgba(0,0,0,0.2); z-index: 2000; display: none; font-weight: bold; opacity: 0; transition: opacity 0.5s; }
        #flash-message.show { display: block; opacity: 1; }
        #flash-message.error { background-color: #dc3545; color: white; }
    </style>
    @endpush

    <div id="flash-message"></div>

    <div class="container mt-4">
        <div id="inicio-container" class="text-center p-5 border rounded-3 bg-light mx-auto" style="max-width: 800px;">
            <h1 class="display-5">{{ $avaliacao->nome }}</h1>
            <p class="fs-5 text-muted">
                @if($avaliacao->tipo == 'simulado')
                    Simulado
                @else
                    Disciplina: {{ $avaliacao->disciplina->nome ?? 'N/A' }}
                @endif
            </p>
            <hr>
            <div class="text-start my-4">
                <h4>Atenção: Regras da Avaliação</h4>
                <p class="mb-2"><strong><i class="bi bi-stopwatch"></i> Tempo Limite:</strong> 
                    @if ($avaliacao->tempo_limite > 0)
                        {{ $avaliacao->tempo_limite }} minutos.
                    @else
                        Ilimitado.
                    @endif
                </p>
                <p>Para garantir a lisura do processo, esta avaliação será realizada em <strong>modo de tela cheia</strong>. As seguintes regras se aplicam:</p>
                <ul>
                    <li>Você <strong>NÃO</strong> poderá sair do modo de tela cheia durante a prova.</li>
                    <li>Alternar para outras abas, usar atalhos como ALT+TAB ou pressionar a tecla ESC será considerado uma infração.</li>
                </ul>
                <p class="fw-bold text-danger">Qualquer tentativa de sair da tela resultará no BLOQUEIO IMEDIATO da prova. Após o bloqueio, você só poderá enviar as respostas já marcadas.</p>
            </div>
            <button type="button" class="btn btn-primary btn-lg" id="btn-iniciar-prova">Estou Ciente e Pronto para Iniciar</button>
        </div>

        <div id="conteudo-prova" style="display: none;">
            @if ($avaliacao->tempo_limite > 0)
                <div id="timer-fixed-container">
                    <i class="bi bi-clock-history"></i> <span id="timer-display">--:--</span>
                </div>
            @endif

            <div class="card shadow-sm">
                <div class="card-header bg-light py-3"><h2 class="card-title mb-0">{{ $avaliacao->nome }}</h2></div>
                <div class="card-body p-4 p-md-5">
                    <form id="form-responder-avaliacao" method="POST" action="{{ route('aluno.avaliacoes.salvar', $avaliacao) }}">
                        @csrf
                        <fieldset id="questoes-fieldset">
                            @foreach ($avaliacao->questoes as $questao)
                                <div class="card mb-4">
                                    <div class="card-header"><strong>Questão {{ $loop->iteration }}: {{ $questao->disciplina->nome ?? '' }}</strong></div>
                                    <div class="card-body">
                                        @if ($questao->imagem_nome)
                                        <div class="text-center mb-3">
                                            <img src="{{ Storage::url($questao->imagem_nome) }}" 
                                                 alt="{{ $questao->imagem_alt ?? 'Imagem da questão ' . $loop->iteration }}" 
                                                 class="img-fluid rounded" style="max-height: 400px; border: 1px solid #dee2e6;">
                                        </div>
                                        @endif
                                        <div class="fs-5 mb-3">{!! nl2br(e($questao->texto)) !!}</div>
                                        <hr>
                                        @if ($questao->tipo == 'multipla_escolha')
                                            <div class="form-check"><input class="form-check-input" type="radio" name="respostas[{{ $questao->id }}]" id="q{{ $questao->id }}A" value="A" required><label class="form-check-label" for="q{{ $questao->id }}A">A) {{ $questao->opcao_a }}</label></div>
                                            <div class="form-check"><input class="form-check-input" type="radio" name="respostas[{{ $questao->id }}]" id="q{{ $questao->id }}B" value="B" required><label class="form-check-label" for="q{{ $questao->id }}B">B) {{ $questao->opcao_b }}</label></div>
                                            <div class="form-check"><input class="form-check-input" type="radio" name="respostas[{{ $questao->id }}]" id="q{{ $questao->id }}C" value="C" required><label class="form-check-label" for="q{{ $questao->id }}C">C) {{ $questao->opcao_c }}</label></div>
                                            <div class="form-check"><input class="form-check-input" type="radio" name="respostas[{{ $questao->id }}]" id="q{{ $questao->id }}D" value="D" required><label class="form-check-label" for="q{{ $questao->id }}D">D) {{ $questao->opcao_d }}</label></div>
                                        @elseif ($questao->tipo == 'verdadeiro_falso')
                                            <div class="form-check"><input class="form-check-input" type="radio" name="respostas[{{ $questao->id }}]" id="q{{ $questao->id }}V" value="V" required><label class="form-check-label" for="q{{ $questao->id }}V">Verdadeiro</label></div>
                                            <div class="form-check"><input class="form-check-input" type="radio" name="respostas[{{ $questao->id }}]" id="q{{ $questao->id }}F" value="F" required><label class="form-check-label" for="q{{ $questao->id }}F">Falso</label></div>
                                        @elseif ($questao->tipo == 'discursiva')
                                            <textarea name="respostas[{{ $questao->id }}]" class="form-control" rows="5" placeholder="Digite sua resposta aqui..." required></textarea>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </fieldset> 
                        <div class="d-grid gap-2 mt-5">
                            <button type="submit" name="finalizar" value="1" id="btn-enviar-avaliacao" class="btn btn-success btn-lg">
                                <i class="bi bi-check2-circle me-2"></i>Enviar Avaliação
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const inicioContainer = document.getElementById('inicio-container');
        const conteudoProva = document.getElementById('conteudo-prova');
        const formProva = document.getElementById('form-responder-avaliacao');
        const btnIniciarProva = document.getElementById('btn-iniciar-prova');
        const flashMessage = document.getElementById('flash-message');
        const timerDisplay = document.getElementById('timer-display');
        const timerContainer = document.getElementById('timer-fixed-container');
        const questoesFieldset = document.getElementById('questoes-fieldset');
        let isProvaIniciada = false;
        let isSubmitting = false;
        let isBlocked = false;

        function showFlashMessage(message, type = 'warning', duration = 4000) {
            flashMessage.textContent = message;
            flashMessage.className = type === 'error' ? 'error' : '';
            flashMessage.classList.add('show');
            if (duration > 0) {
                setTimeout(() => flashMessage.classList.remove('show'), duration);
            }
        }

        function bloquearProva(motivo) {
            if (isBlocked) return;
            isBlocked = true;
            showFlashMessage(`PROVA BLOQUEADA: ${motivo}. Envie suas respostas agora.`, 'error', 0);
            if (questoesFieldset) questoesFieldset.disabled = true;
            document.removeEventListener('fullscreenchange', onFullscreenChange);
            document.removeEventListener('visibilitychange', onVisibilityChange);
            document.removeEventListener('keydown', onKeyDown);
        }

        function iniciarCronometro(minutos) {
            if (!minutos || minutos <= 0 || !timerDisplay) return;
            let tempoTotalSegundos = minutos * 60;
            const countdown = setInterval(function() {
                if (isSubmitting || isBlocked) {
                    clearInterval(countdown); return;
                }
                if (tempoTotalSegundos <= 0) {
                    clearInterval(countdown);
                    bloquearProva('Tempo esgotado');
                    formProva.submit();
                } else {
                    tempoTotalSegundos--;
                    const mins = Math.floor(tempoTotalSegundos / 60);
                    const secs = tempoTotalSegundos % 60;
                    timerDisplay.textContent = `${String(mins).padStart(2, '0')}:${String(secs).padStart(2, '0')}`;
                    if (timerContainer) {
                        timerContainer.classList.remove('timer-alerta', 'timer-critico');
                        if (tempoTotalSegundos <= 60) {
                            timerContainer.classList.add('timer-critico');
                        } else if (tempoTotalSegundos <= 300) {
                            timerContainer.classList.add('timer-alerta');
                        }
                    }
                }
            }, 1000);
        }
        
        function onFullscreenChange() {
            if (!document.fullscreenElement && isProvaIniciada) {
                bloquearProva("Saída da tela cheia");
            }
        }
        function onVisibilityChange() {
            if (document.hidden && isProvaIniciada) {
                bloquearProva("Troca de aba/aplicativo");
            }
        }
        function onKeyDown(event) {
            if (event.key === 'Escape' || (event.altKey && event.key === 'Tab')) {
                event.preventDefault();
                bloquearProva(`Uso de tecla de atalho (${event.key})`);
            }
        }

        function ativarMonitoramento() {
            document.addEventListener('fullscreenchange', onFullscreenChange);
            document.addEventListener('visibilitychange', onVisibilityChange);
            document.addEventListener('keydown', onKeyDown);
            window.addEventListener('beforeunload', (event) => {
                if (isProvaIniciada && !isSubmitting) {
                    event.preventDefault();
                    event.returnValue = '';
                }
            });
        }

        if (btnIniciarProva) {
            btnIniciarProva.addEventListener('click', function() {
                document.documentElement.requestFullscreen().then(() => {
                    isProvaIniciada = true;
                    inicioContainer.style.display = 'none';
                    conteudoProva.style.display = 'block';
                    ativarMonitoramento();
                    iniciarCronometro({{ $avaliacao->tempo_limite ?? 0 }});
                }).catch(err => {
                    showFlashMessage('Erro: Não foi possível ativar o modo de tela cheia. Verifique as permissões do navegador.', 'error', 5000);
                });
            });
        }

        if (formProva) {
            formProva.addEventListener('submit', () => {
                isSubmitting = true;
            });
        }
    });
    </script>
    @endpush
</x-app-layout>