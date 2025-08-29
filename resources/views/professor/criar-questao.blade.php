<x-app-layout>
    <x-slot name="title">Criar Nova Questão</x-slot>

    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="h3">Criar Nova Questão</h1>
            <a href="{{ route('professor.banco-questoes.index') }}" class="btn btn-secondary"><i class="bi bi-arrow-left me-1"></i> Voltar ao Banco</a>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                @if ($errors->any())
                <div class="alert alert-danger mb-4">
                    <h6>Ocorreu um erro:</h6>
                    <ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                </div>
                @endif

                <form method="POST" action="{{ route('professor.banco-questoes.store') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="disciplina_id" class="form-label">1. Disciplina *</label>
                            <select class="form-select" id="disciplina_id" name="disciplina_id" required>
                                <option value="" disabled selected>-- Selecione --</option>
                                @foreach ($disciplinas as $disciplina)
                                <option value="{{ $disciplina->id }}" {{ old('disciplina_id') == $disciplina->id ? 'selected' : '' }}>{{ $disciplina->nome }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="series_ids" class="form-label">2. Série(s) de Aplicação *</label>
                            <select class="form-select" id="series_ids" name="series_ids[]" required multiple size="3">
                                @foreach ($series as $serie)
                                <option value="{{ $serie->id }}" {{ in_array($serie->id, old('series_ids', [])) ? 'selected' : '' }}>{{ $serie->nome }}</option>
                                @endforeach
                            </select>
                            <div class="form-text">Segure Ctrl (ou Cmd) para selecionar mais de uma.</div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="assunto" class="form-label">3. Assunto *</label>
                        <input type="text" class="form-control" id="assunto" name="assunto" value="{{ old('assunto') }}" required>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="tipo" class="form-label">4. Tipo de Questão *</label>
                            <select class="form-select" id="tipo" name="tipo" required>
                                <option value="multipla_escolha" {{ old('tipo', 'multipla_escolha') == 'multipla_escolha' ? 'selected' : '' }}>Múltipla Escolha</option>
                                <option value="verdadeiro_falso" {{ old('tipo') == 'verdadeiro_falso' ? 'selected' : '' }}>Verdadeiro ou Falso</option>
                                <option value="discursiva" {{ old('tipo') == 'discursiva' ? 'selected' : '' }}>Discursiva</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="nivel" class="form-label">5. Nível de Dificuldade *</label>
                            <select class="form-select" id="nivel" name="nivel" required>
                                <option value="facil" {{ old('nivel') == 'facil' ? 'selected' : '' }}>Fácil</option>
                                <option value="media" {{ old('nivel', 'media') == 'media' ? 'selected' : '' }}>Média</option>
                                <option value="dificil" {{ old('nivel') == 'dificil' ? 'selected' : '' }}>Difícil</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="texto_questao" class="form-label">6. Enunciado da Questão *</label>
                        <textarea class="form-control" id="texto_questao" name="texto_questao" rows="5" required>{{ old('texto_questao') }}</textarea>
                    </div>
                    <div class="mb-3 p-3 border rounded bg-light">
                        <p class="fw-bold mb-2">7. Imagem da Questão (Opcional)</p>
                        <div class="mb-2"><input class="form-control" type="file" id="imagem_questao" name="imagem_questao" accept="image/png, image/jpeg, image/gif"></div>
                        <div><label for="imagem_alt" class="form-label">Descrição da imagem (acessibilidade)</label><input type="text" class="form-control" id="imagem_alt" name="imagem_alt" value="{{ old('imagem_alt') }}" placeholder="Ex: Gráfico de barras crescente..."></div>
                    </div>
                    <div id="campos-resposta">
                        <div id="opcoes-multipla-escolha" class="mb-3">
                            <label class="form-label fw-bold">Opções de Resposta *</label>
                            <div class="input-group mb-2"><span class="input-group-text">A</span><input type="text" class="form-control" name="opcao_a" value="{{ old('opcao_a') }}"></div>
                            <div class="input-group mb-2"><span class="input-group-text">B</span><input type="text" class="form-control" name="opcao_b" value="{{ old('opcao_b') }}"></div>
                            <div class="input-group mb-2"><span class="input-group-text">C</span><input type="text" class="form-control" name="opcao_c" value="{{ old('opcao_c') }}"></div>
                            <div class="input-group"><span class="input-group-text">D</span><input type="text" class="form-control" name="opcao_d" value="{{ old('opcao_d') }}"></div>
                        </div>
                        <div id="campo-gabarito" class="mb-3">
                            <label for="gabarito" class="form-label">Gabarito *</label>
                            <input type="text" class="form-control" id="gabarito" name="gabarito" value="{{ old('gabarito') }}" style="text-transform:uppercase" maxlength="1">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="justificativa_gabarito" class="form-label">Justificativa do Gabarito (Opcional)</label>
                        <textarea class="form-control" id="justificativa_gabarito" name="justificativa_gabarito" rows="3">{{ old('justificativa_gabarito') }}</textarea>
                    </div>
                    <div class="d-grid"><button type="submit" class="btn btn-primary btn-lg"><i class="bi bi-check-circle me-2"></i>Salvar Questão</button></div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        {{-- Seu script JS para os campos dinâmicos continua o mesmo --}}
    @endpush
</x-app-layout>