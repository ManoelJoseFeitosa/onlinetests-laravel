<x-app-layout>
    <div class="container">
        {{-- ... cabeçalho ... --}}
        <div class="card shadow-sm"><div class="card-body">
            @if ($errors->any()) {{-- ... bloco de erros ... --}} @endif
            <form method="POST" action="{{ route('professor.banco-questoes.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="disciplina_id" class="form-label">1. Disciplina *</label>
                        <select class="form-select @error('disciplina_id') is-invalid @enderror" id="disciplina_id" name="disciplina_id" required>
                            {{-- ... opções de disciplina ... --}}
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="series_ids" class="form-label">2. Série(s) de Aplicação *</label>
                        {{-- CORREÇÃO: Campo de seleção múltipla --}}
                        <select class="form-select @error('series_ids') is-invalid @enderror" id="series_ids" name="series_ids[]" required multiple size="3">
                            @foreach ($series as $serie)
                            <option value="{{ $serie->id }}" {{ in_array($serie->id, old('series_ids', [])) ? 'selected' : '' }}>{{ $serie->nome }}</option>
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