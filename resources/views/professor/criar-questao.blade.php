<x-app-layout>
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="h3">Criar Nova Questão</h1>
            <a href="{{ route('professor.banco-questoes.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-1"></i> Voltar ao Banco
            </a>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">

                {{-- CÓDIGO ADICIONADO: Bloco para exibir erros de validação --}}
                @if ($errors->any())
                    <div class="alert alert-danger mb-4" role="alert">
                        <h4 class="alert-heading">Ops! Ocorreu um erro.</h4>
                        <p>Por favor, verifique os campos abaixo e tente novamente.</p>
                        <hr>
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('professor.banco-questoes.store') }}" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="disciplina_id" class="form-label">1. Disciplina *</label>
                            <select class="form-select @error('disciplina_id') is-invalid @enderror" id="disciplina_id" name="disciplina_id" required>
                                <option value="" disabled selected>-- Selecione --</option>
                                @foreach ($disciplinas as $disciplina)
                                <option value="{{ $disciplina->id }}" {{ old('disciplina_id') == $disciplina->id ? 'selected' : '' }}>{{ $disciplina->nome }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="serie_id" class="form-label">2. Série de Aplicação *</label>
                            <select class="form-select @error('serie_id') is-invalid @enderror" id="serie_id" name="serie_id" required>
                                <option value="" disabled selected>-- Selecione --</option>
                                @foreach ($series as $serie)
                                <option value="{{ $serie->id }}" {{ old('serie_id') == $serie->id ? 'selected' : '' }}>{{ $serie->nome }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="assunto" class="form-label">3. Assunto *</label>
                        <input type="text" class="form-control @error('assunto') is-invalid @enderror" id="assunto" name="assunto" value="{{ old('assunto') }}" required>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="tipo" class="form-label">4. Tipo de Questão *</label>
                            <select class="form-select @error('tipo') is-invalid @enderror" id="tipo" name="tipo" required>
                                <option value="multipla_escolha" {{ old('tipo') == 'multipla_escolha' ? 'selected' : '' }}>Múltipla Escolha</option>
                                <option value="verdadeiro_falso" {{ old('tipo') == 'verdadeiro_falso' ? 'selected' : '' }}>Verdadeiro ou Falso</option>
                                <option value="discursiva" {{ old('tipo') == 'discursiva' ? 'selected' : '' }}>Discursiva</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="nivel" class="form-label">5. Nível de Dificuldade *</label>
                            <select class="form-select @error('nivel') is-invalid @enderror" id="nivel" name="nivel" required>
                                <option value="facil" {{ old('nivel') == 'facil' ? 'selected' : '' }}>Fácil</option>
                                <option value="media" {{ old('nivel', 'media') == 'media' ? 'selected' : '' }}>Média</option>
                                <option value="dificil" {{ old('nivel') == 'dificil' ? 'selected' : '' }}>Difícil</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="texto_questao" class="form-label">6. Enunciado da Questão *</label>
                        <textarea class="form-control @error('texto_questao') is-invalid @enderror" id="texto_questao" name="texto_questao" rows="5" required>{{ old('texto_questao') }}</textarea>
                    </div>

                    <div class="mb-3 p-3 border rounded bg-light">
                        <p class="fw-bold mb-2">7. Imagem da Questão (Opcional)</p>
                        <div class="mb-2">
                            <input class="form-control @error('imagem_questao') is-invalid @enderror" type="file" id="imagem_questao" name="imagem_questao" accept="image/png, image/jpeg, image/gif">
                        </div>
                        <div>
                            <label for="imagem_alt" class="form-label">Descrição da imagem (acessibilidade)</label>
                            <input type="text" class="form-control" id="imagem_alt" name="imagem_alt" value="{{ old('imagem_alt') }}" placeholder="Ex: Gráfico de barras crescente...">
                        </div>
                    </div>
                    
                    <div id="campos-resposta">
                        <div id="opcoes-multipla-escolha" class="mb-3">
                            <label class="form-label fw-bold">Opções de Resposta *</label>
                            <div class="input-group mb-2"><span class="input-group-text">A</span><input type="text" class="form-control" name="opcao_a" value="{{ old('opcao_a') }}" placeholder="Texto da opção A"></div>
                            <div class="input-group mb-2"><span class="input-group-text">B</span><input type="text" class="form-control" name="opcao_b" value="{{ old('opcao_b') }}" placeholder="Texto da opção B"></div>
                            <div class="input-group mb-2"><span class="input-group-text">C</span><input type="text" class="form-control" name="opcao_c" value="{{ old('opcao_c') }}" placeholder="Texto da opção C"></div>
                            <div class="input-group"><span class="input-group-text">D</span><input type="text" class="form-control" name="opcao_d" value="{{ old('opcao_d') }}" placeholder="Texto da opção D"></div>
                        </div>

                        <div id="campo-gabarito" class="mb-3">
                            <label for="gabarito" class="form-label">Gabarito *</label>
                            <input type="text" class="form-control @error('gabarito') is-invalid @enderror" id="gabarito" name="gabarito" value="{{ old('gabarito') }}" style="text-transform:uppercase" maxlength="1">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="justificativa_gabarito" class="form-label">Justificativa do Gabarito (Opcional)</label>
                        <textarea class="form-control" id="justificativa_gabarito" name="justificativa_gabarito" rows="3" placeholder="Explique por que a alternativa correta é a certa.">{{ old('justificativa_gabarito') }}</textarea>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg"><i class="bi bi-check-circle me-2"></i>Salvar Questão</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const tipoQuestaoSelect = document.getElementById('tipo');
        const opcoesMultiplaEscolha = document.getElementById('opcoes-multipla-escolha');
        const campoGabarito = document.getElementById('campo-gabarito');
        const inputGabarito = document.getElementById('gabarito');
        const inputOpcaoA = document.getElementsByName('opcao_a')[0];
        const inputOpcaoB = document.getElementsByName('opcao_b')[0];
        const inputOpcaoC = document.getElementsByName('opcao_c')[0];
        const inputOpcaoD = document.getElementsByName('opcao_d')[0];

        function toggleCamposPorTipo() {
            const tipoSelecionado = tipoQuestaoSelect.value;

            // Reseta todos para o padrão
            opcoesMultiplaEscolha.style.display = 'none';
            campoGabarito.style.display = 'none';
            inputOpcaoA.required = false;
            inputOpcaoB.required = false;
            inputOpcaoC.required = false;
            inputOpcaoD.required = false;
            inputGabarito.required = false;

            if (tipoSelecionado === 'multipla_escolha') {
                opcoesMultiplaEscolha.style.display = 'block';
                campoGabarito.style.display = 'block';
                inputGabarito.placeholder = 'Gabarito (A, B, C ou D)';
                inputOpcaoA.required = true;
                inputOpcaoB.required = true;
                inputOpcaoC.required = true;
                inputOpcaoD.required = true;
                inputGabarito.required = true;
            } else if (tipoSelecionado === 'verdadeiro_falso') {
                campoGabarito.style.display = 'block';
                inputGabarito.placeholder = 'Gabarito (V ou F)';
                inputGabarito.required = true;
            } else if (tipoSelecionado === 'discursiva') {
                // Para discursiva, tudo fica escondido
            }
        }

        tipoQuestaoSelect.addEventListener('change', toggleCamposPorTipo);
        toggleCamposPorTipo(); // Chama a função na carga da página
    });
    </script>
    @endpush
</x-app-layout>
