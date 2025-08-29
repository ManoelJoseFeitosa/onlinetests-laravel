<x-app-layout>
    <x-slot name="title">Editar Questão #{{ $questao->id }}</x-slot>

    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">Editar Questão #{{ $questao->id }}</h2>
            <a href="{{ route('professor.banco-questoes.index') }}" class="btn btn-outline-secondary">&larr; Voltar ao Banco de Questões</a>
        </div>

        <div class="card shadow-sm">
            <div class="card-body p-4">
                <form method="POST" action="{{ route('professor.banco-questoes.update', $questao) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="disciplina_id" class="form-label">Disciplina</label>
                            <select class="form-select" id="disciplina_id" name="disciplina_id" required>
                                @foreach ($disciplinas as $disciplina)
                                <option value="{{ $disciplina->id }}" @if($disciplina->id == old('disciplina_id', $questao->disciplina_id)) selected @endif>{{ $disciplina->nome }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="series_ids" class="form-label">Série(s) de Aplicação</label>
                            <select class="form-select" id="series_ids" name="series_ids[]" required multiple size="3">
                                @php $seriesSelecionadas = old('series_ids', $questao->series->pluck('id')->toArray()); @endphp
                                @foreach ($series as $serie)
                                <option value="{{ $serie->id }}" {{ in_array($serie->id, $seriesSelecionadas) ? 'selected' : '' }}>{{ $serie->nome }}</option>
                                @endforeach
                            </select>
                            <div class="form-text">Segure Ctrl (ou Cmd) para selecionar.</div>
                        </div>
                    </div>

                    {{-- O restante do formulário é idêntico ao de criação, mas com os valores preenchidos --}}
                    <div class="mb-3">
                        <label for="assunto" class="form-label">Assunto</label>
                        <input type="text" class="form-control" id="assunto" name="assunto" value="{{ old('assunto', $questao->assunto) }}" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="tipo" class="form-label">Tipo de Questão</label>
                            <select class="form-select" id="tipo" name="tipo" required>
                                <option value="multipla_escolha" @if(old('tipo', $questao->tipo) == 'multipla_escolha') selected @endif>Múltipla Escolha</option>
                                <option value="discursiva" @if(old('tipo', $questao->tipo) == 'discursiva') selected @endif>Discursiva</option>
                                <option value="verdadeiro_falso" @if(old('tipo', $questao->tipo) == 'verdadeiro_falso') selected @endif>Verdadeiro ou Falso</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="nivel" class="form-label">Nível de Dificuldade</label>
                            <select class="form-select" id="nivel" name="nivel" required>
                                <option value="facil" @if(old('nivel', $questao->nivel) == 'facil') selected @endif>Fácil</option>
                                <option value="media" @if(old('nivel', $questao->nivel) == 'media') selected @endif>Média</option>
                                <option value="dificil" @if(old('nivel', $questao->nivel) == 'dificil') selected @endif>Difícil</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="texto_questao" class="form-label">Enunciado da Questão</label>
                        <textarea class="form-control" id="texto_questao" name="texto_questao" rows="5" required>{{ old('texto_questao', $questao->texto) }}</textarea>
                    </div>
                    <div id="opcoes_multipla_escolha">
                        <hr><h6>Opções de Resposta</h6>
                        <div class="row">
                            <div class="col-md-6 mb-3"><label for="opcao_a" class="form-label">Opção A</label><textarea class="form-control" name="opcao_a">{{ old('opcao_a', $questao->opcao_a) }}</textarea></div>
                            <div class="col-md-6 mb-3"><label for="opcao_b" class="form-label">Opção B</label><textarea class="form-control" name="opcao_b">{{ old('opcao_b', $questao->opcao_b) }}</textarea></div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3"><label for="opcao_c" class="form-label">Opção C</label><textarea class="form-control" name="opcao_c">{{ old('opcao_c', $questao->opcao_c) }}</textarea></div>
                            <div class="col-md-6 mb-3"><label for="opcao_d" class="form-label">Opção D</label><textarea class="form-control" name="opcao_d">{{ old('opcao_d', $questao->opcao_d) }}</textarea></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="gabarito" class="form-label">Gabarito</label>
                        <input type="text" class="form-control" id="gabarito" name="gabarito" value="{{ old('gabarito', $questao->gabarito) }}">
                        <div class="form-text">Para múltipla escolha, use A, B, C ou D. Para V/F, use V ou F. Para discursiva, deixe em branco.</div>
                    </div>
                    <div class="mb-3">
                        <label for="justificativa_gabarito" class="form-label">Justificativa (Opcional)</label>
                        <textarea class="form-control" id="justificativa_gabarito" name="justificativa_gabarito" rows="3">{{ old('justificativa_gabarito', $questao->justificativa_gabarito) }}</textarea>
                    </div>
                    <hr>
                    <div class="mb-3">
                        <label for="imagem_questao" class="form-label">Substituir Imagem (Opcional)</label>
                        <input class="form-control" type="file" id="imagem_questao" name="imagem_questao">
                        @if ($questao->imagem_nome)
                            <div class="mt-2"><p class="mb-1">Imagem atual:</p><img src="{{ asset('storage/uploads/questoes/' . $questao->imagem_nome) }}" alt="Imagem da questão" style="max-width: 200px; max-height: 200px; border-radius: 5px; border: 1px solid #ddd;"></div>
                        @endif
                    </div>
                    <div class="mb-3">
                        <label for="imagem_alt" class="form-label">Descrição da Imagem (Acessibilidade)</label>
                        <input type="text" class="form-control" id="imagem_alt" name="imagem_alt" value="{{ old('imagem_alt', $questao->imagem_alt) }}">
                    </div>
                    <div class="text-center mt-4">
                        <a href="{{ route('professor.banco-questoes.index') }}" class="btn btn-secondary">Cancelar</a>
                        <button type="submit" class="btn btn-primary btn-lg"><i class="bi bi-save-fill me-2"></i>Salvar Alterações</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        {{-- Seu script JS para os campos dinâmicos continua o mesmo --}}
    @endpush
</x-app-layout>