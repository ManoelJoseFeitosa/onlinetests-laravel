@section('title', 'Criar Modelo de Avaliação')
<x-app-layout>
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Criar Modelo de Avaliação</h2>
            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">&larr; Voltar ao Painel</a>
        </div>
        <p class="text-muted">Crie um modelo com regras para gerar provas e simulados dinâmicos para cada aluno.</p>
        <hr>

        <form id="modelo-form" method="POST" action="{{ route('coordenador.modelos.store') }}">
            @csrf
            <div class="card shadow-sm mb-4">
                <div class="card-header"><h5 class="mb-0">1. Informações Gerais</h5></div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="nome_modelo" class="form-label">Nome do Modelo</label>
                            <input type="text" name="nome_modelo" id="nome_modelo" class="form-control" required placeholder="Ex: Prova Mensal de Biologia - 1º Ano">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="serie_id" class="form-label">Série de Aplicação</label>
                            <select id="serie_id" name="serie_id" class="form-select" required>
                                <option value="" selected disabled>-- Selecione uma Série --</option>
                                @foreach ($series as $serie)<option value="{{ $serie->id }}">{{ $serie->nome }}</option>@endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="tipo_modelo" class="form-label">Tipo de Modelo</label>
                            <select id="tipo_modelo" name="tipo_modelo" class="form-select" required>
                                <option value="prova" selected>Prova (Única Disciplina)</option>
                                <option value="simulado">Simulado (Múltiplas Disciplinas)</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="tempo_limite" class="form-label">Tempo Limite (em minutos)</label>
                            <input type="number" class="form-control" id="tempo_limite" name="tempo_limite" min="1" placeholder="Opcional. Ex: 90">
                        </div>
                    </div>
                </div>
            </div>
            <div id="regras-container">
                {{-- O conteúdo das regras será inserido aqui pelo JavaScript --}}
            </div>
            <div class="text-center mt-4">
                <button type="submit" class="btn btn-primary btn-lg">Salvar Modelo de Avaliação</button>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- ELEMENTOS DO DOM ---
        const tipoSelect = document.getElementById('tipo_modelo');
        const serieSelect = document.getElementById('serie_id');
        const regrasContainer = document.getElementById('regras-container');
        const form = document.getElementById('modelo-form');

        // --- DADOS E ROTAS VINDOS DO LARAVEL ---
        const allDisciplinas = @json($disciplinas->map(fn($d) => ['id' => $d->id, 'nome' => $d->nome]));
        const assuntosApiUrl = "{{ route('coordenador.modelos.api.assuntos') }}";
        const simuladoApiUrlBase = "{{ route('coordenador.modelos.api.conteudo-simulado', ['serie' => ':serieId']) }}";

        // --- FUNÇÕES DE RENDERIZAÇÃO ---
        function renderizarRegrasProva() {
            const disciplinaOptions = allDisciplinas.map(d => `<option value="${d.id}">${d.nome}</option>`).join('');
            const provaHTML = `
            <div class="card shadow-sm">
                <div class="card-header"><h5 class="mb-0">2. Regras de Geração da Prova</h5></div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="disciplina_id_0" class="form-label">Disciplina</label>
                            <select id="disciplina_id_0" name="disciplina_id_0" class="form-select" required>
                                <option value="" selected disabled>-- Selecione a Disciplina --</option>
                                ${disciplinaOptions}
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="assuntos_0" class="form-label">Assuntos (segure Ctrl ou Shift para selecionar vários)</label>
                        <select name="regra_0_assunto_0[]" id="assuntos_0" class="form-select" multiple required size="6">
                            <option disabled>Selecione uma disciplina para ver os assuntos.</option>
                        </select>
                    </div>
                    <fieldset class="border p-3 rounded mt-3">
                        <legend class="fs-6 fw-bold px-2">Quantidade de Questões por Nível</legend>
                        <div class="row">
                            <div class="col-md-4 mb-2"><label for="qtd_facil" class="form-label text-success">Fáceis</label><input type="number" name="regra_0_nivel_facil_qtd" id="qtd_facil" class="form-control" min="0" value="0" required></div>
                            <div class="col-md-4 mb-2"><label for="qtd_media" class="form-label text-primary">Médias</label><input type="number" name="regra_0_nivel_media_qtd" id="qtd_media" class="form-control" min="0" value="0" required></div>
                            <div class="col-md-4 mb-2"><label for="qtd_dificil" class="form-label text-danger">Difíceis</label><input type="number" name="regra_0_nivel_dificil_qtd" id="qtd_dificil" class="form-control" min="0" value="0" required></div>
                        </div>
                    </fieldset>
                </div>
            </div>`;
            regrasContainer.innerHTML = provaHTML;
            // Adiciona o listener para o novo campo de disciplina
            document.getElementById('disciplina_id_0').addEventListener('change', fetchAssuntos);
        }

        // --- FUNÇÕES DE BUSCA (API) ---
        function fetchAssuntos() {
            const disciplinaId = document.getElementById('disciplina_id_0').value;
            const assuntosSelect = document.getElementById('assuntos_0');
            const serieId = serieSelect.value; // Pega o valor da série selecionada no topo da página

            if (!disciplinaId || !serieId) {
                assuntosSelect.innerHTML = '<option disabled>Selecione uma disciplina para ver os assuntos.</option>';
                return;
            }

            assuntosSelect.innerHTML = '<option disabled>Carregando...</option>';
            
            // CORREÇÃO: Constrói a URL com ambos os parâmetros
            const url = `${assuntosApiUrl}?disciplina_id=${disciplinaId}&serie_id=${serieId}`;

            fetch(url)
                .then(response => {
                    if (!response.ok) throw new Error('Erro de rede ou servidor.');
                    return response.json();
                })
                .then(data => {
                    if (data.assuntos && data.assuntos.length > 0) {
                        assuntosSelect.innerHTML = data.assuntos.map(assunto => `<option value="${assunto}">${assunto}</option>`).join('');
                    } else {
                        assuntosSelect.innerHTML = '<option disabled>Nenhum assunto com questões encontrado para esta série/disciplina.</option>';
                    }
                })
                .catch(error => {
                    console.error('Falha ao buscar assuntos:', error);
                    assuntosSelect.innerHTML = '<option disabled>Erro ao carregar assuntos.</option>';
                });
        }

        // --- LÓGICA PRINCIPAL E EVENT LISTENERS ---
        function atualizarUI() {
            const tipo = tipoSelect.value;
            const serieId = serieSelect.value;

            if (!serieId) {
                regrasContainer.innerHTML = '<div class="alert alert-info">Por favor, selecione uma série para definir as regras.</div>';
                return;
            }

            if (tipo === 'prova') {
                renderizarRegrasProva();
            } else if (tipo === 'simulado') {
                // A lógica de simulado pode ser adicionada aqui depois
                 regrasContainer.innerHTML = ''; // Limpa para não mostrar regras de prova
                // Chame aqui a sua função fetchConteudoSimulado() se/quando implementada
            }
        }

        // Adiciona os listeners principais
        tipoSelect.addEventListener('change', atualizarUI);
        serieSelect.addEventListener('change', atualizarUI);

        // Inicializa a UI ao carregar a página
        atualizarUI();
    });
    </script>
    @endpush
</x-app-layout>
