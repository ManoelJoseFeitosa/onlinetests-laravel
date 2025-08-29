<x-app-layout>
    <div class="container">
        {{-- ... cabeçalho ... --}}
        <div class="card shadow-sm"><div class="card-body p-4">
            <form method="POST" action="{{ route('professor.banco-questoes.update', $questao) }}" enctype="multipart/form-data">
                @csrf @method('PUT')
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="disciplina_id" class="form-label">Disciplina</label>
                        <select class="form-select" id="disciplina_id" name="disciplina_id" required>
                            {{-- ... opções de disciplina ... --}}
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="series_ids" class="form-label">Série(s) de Aplicação</label>
                        {{-- CORREÇÃO: Campo de seleção múltipla com valores pré-selecionados --}}
                        <select class="form-select" id="series_ids" name="series_ids[]" required multiple size="3">
                            @php $seriesSelecionadas = old('series_ids', $questao->series->pluck('id')->toArray()); @endphp
                            @foreach ($series as $serie)
                            <option value="{{ $serie->id }}" {{ in_array($serie->id, $seriesSelecionadas) ? 'selected' : '' }}>
                                {{ $serie->nome }}
                            </option>
                            @endforeach
                        </select>
                        <div class="form-text">Segure Ctrl (ou Cmd) para selecionar mais de uma.</div>
                    </div>
                </div>
                {{-- ... restante do formulário ... --}}
            </form>
        </div></div>
    </div>
    @push('scripts') {{-- ... seu script JS ... --}} @endpush
</x-app-layout>