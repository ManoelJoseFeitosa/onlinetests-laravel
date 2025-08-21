@section('title', 'Desempenho do Aluno')
<x-app-layout>
    <div id="pdf-metadata" data-escola="{{ $escola_nome }}" style="display: none;"></div>

    <div class="container">
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Painel de Desempenho</h1>
            <button id="gerar-pdf" class="btn btn-danger btn-icon-split btn-sm">
                <span class="icon text-white-50"><i class="bi bi-file-earmark-pdf-fill"></i></span>
                <span class="text">Gerar PDF</span>
            </button>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">Filtros de Análise</h6></div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-5 mb-3">
                        <label for="filtro-turma" class="form-label">Selecione a Turma:</label>
                        <select id="filtro-turma" class="form-select"><option value="">-- Selecione uma turma --</option></select>
                    </div>
                    <div class="col-md-5 mb-3">
                        <label for="filtro-aluno" class="form-label">Selecione o Aluno:</label>
                        <select id="filtro-aluno" class="form-select" disabled><option value="">-- Todos da Turma --</option></select>
                    </div>
                </div>
            </div>
        </div>

        <div id="charts-container">
            <div id="mensagem-inicial" class="text-center py-5">
                <i class="bi bi-graph-up fa-3x text-gray-400 mb-3"></i>
                <p class="text-gray-600">Selecione uma turma para visualizar o desempenho geral.</p>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const filtroTurma = document.getElementById('filtro-turma');
        const filtroAluno = document.getElementById('filtro-aluno');
        const chartsContainer = document.getElementById('charts-container');
        const btnGerarPdf = document.getElementById('gerar-pdf');
        let activeCharts = [];

        const urls = {
            turmas: "{{ route('coordenador.desempenho.api.turmas') }}",
            alunos: "{{ route('coordenador.desempenho.api.alunos', ['serie' => ':id']) }}",
            desempenhoTurma: "{{ route('coordenador.desempenho.api.turma.dados', ['serie' => ':id']) }}",
            desempenhoAluno: "{{ route('coordenador.desempenho.api.aluno.dados', ['user' => ':id']) }}"
        };

        function limparVisualizacao() {
            activeCharts.forEach(chart => chart.destroy());
            activeCharts = [];
            chartsContainer.innerHTML = '';
        }

        function exibirMensagem(texto, tipo = 'info') {
            limparVisualizacao();
            let iconClass = 'bi-info-circle-fill';
            if (tipo === 'loading') iconClass = 'bi-arrow-repeat';
            if (tipo === 'error') iconClass = 'bi-exclamation-triangle-fill';
            chartsContainer.innerHTML = `<div class="text-center py-5"><i class="bi ${iconClass} fs-1 text-muted mb-3"></i><p class="text-muted">${texto}</p></div>`;
        }

        function criarWrapperGrafico(id, titulo) {
            const wrapper = document.createElement('div');
            wrapper.className = 'col-lg-6 mb-4';
            wrapper.innerHTML = `
                <div class="card shadow h-100">
                    <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">${titulo}</h6></div>
                    <div class="card-body"><div class="chart-area" style="position: relative; height:320px;"><canvas id="${id}"></canvas></div></div>
                </div>`;
            return wrapper;
        }

        function renderizarGrafico(ctx, tipo, data, options) {
            const chart = new Chart(ctx, { type: tipo, data: data, options: options });
            activeCharts.push(chart);
        }

        async function carregarTurmas() {
            try {
                const response = await fetch(urls.turmas);
                if (!response.ok) throw new Error('Falha ao buscar turmas.');
                const turmas = await response.json();
                turmas.forEach(turma => filtroTurma.add(new Option(turma.nome, turma.id)));
            } catch (error) {
                alert('Não foi possível carregar as turmas.');
            }
        }

        filtroTurma.addEventListener('change', async function() {
            // ... (lógica para carregar alunos e gráfico da turma)
            const turmaId = this.value;
            filtroAluno.innerHTML = '<option value="">-- Todos da Turma --</option>';
            filtroAluno.disabled = true;

            if (!turmaId) {
                exibirMensagem('Selecione uma turma para visualizar o desempenho geral.');
                return;
            }

            exibirMensagem('Carregando dados...', 'loading');

            try {
                const alunosResponse = await fetch(urls.alunos.replace(':id', turmaId));
                const alunos = await alunosResponse.json();
                alunos.forEach(aluno => filtroAluno.add(new Option(aluno.nome, aluno.id)));
                filtroAluno.disabled = false;

                // Gráfico da turma aqui (se necessário)
                limparVisualizacao(); // Limpa para o caso de não ter dados de turma
                exibirMensagem('Selecione um aluno para ver os detalhes de desempenho.');

            } catch (error) {
                exibirMensagem('Ocorreu um erro ao buscar os dados da turma.', 'error');
            }
        });

        filtroAluno.addEventListener('change', async function() {
            const alunoId = this.value;
            const turmaId = filtroTurma.value;

            if (!alunoId) { // Se voltou para "Todos da Turma"
                // Recarregar gráfico da turma (opcional)
                exibirMensagem('Selecione um aluno para ver os detalhes de desempenho.');
                return;
            }

            exibirMensagem('Carregando dados do aluno...', 'loading');

            try {
                const response = await fetch(urls.desempenhoAluno.replace(':id', alunoId));
                if (!response.ok) throw new Error('Falha ao buscar dados do aluno.');
                const dados = await response.json();

                limparVisualizacao();
                const row = document.createElement('div');
                row.className = 'row';
                chartsContainer.appendChild(row);

                // Gráfico 1 - Evolução
                const wrapperGeral = criarWrapperGrafico('alunoGeralChart', `Evolução de Notas: ${this.options[this.selectedIndex].text}`);
                row.appendChild(wrapperGeral);
                const ctxGeral = document.getElementById('alunoGeralChart').getContext('2d');
                renderizarGrafico(ctxGeral, 'line',
                    { labels: dados.geral.labels, datasets: [{ label: 'Nota', data: dados.geral.data, borderColor: '#1cc88a', tension: 0.1, fill: false }] },
                    { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true, max: 10 } } }
                );

                // Gráfico 2 - Por Nível
                const wrapperNivel = criarWrapperGrafico('alunoNivelChart', `Percentual de Acertos por Nível`);
                row.appendChild(wrapperNivel);
                const ctxNivel = document.getElementById('alunoNivelChart').getContext('2d');
                renderizarGrafico(ctxNivel, 'bar',
                    { labels: dados.nivel.labels, datasets: [{ label: '% de Acertos', data: dados.nivel.data, backgroundColor: ['#1cc88a', '#f6c23e', '#e74a3b'] }] },
                    { responsive: true, maintainAspectRatio: false, indexAxis: 'y', scales: { x: { beginAtZero: true, max: 100 } } }
                );

            } catch (error) {
                exibirMensagem('Ocorreu um erro ao buscar os dados do aluno.', 'error');
            }
        });

        // Lógica do PDF...
        btnGerarPdf.addEventListener('click', function() {
            const { jsPDF } = window.jspdf;
            const container = document.getElementById('charts-container');
            if (!container.querySelector('canvas')) { alert("Não há gráficos para gerar o PDF."); return; }
            const escolaNome = document.getElementById('pdf-metadata').dataset.escola;
            const turmaNome = filtroTurma.options[filtroTurma.selectedIndex].text;
            const alunoNome = filtroAluno.value ? filtroAluno.options[filtroAluno.selectedIndex].text : "Desempenho Geral da Turma";
            const dataGeracao = new Date().toLocaleDateString('pt-BR');
            btnGerarPdf.disabled = true;
            btnGerarPdf.querySelector('.text').textContent = 'Gerando...';

            html2canvas(container, { scale: 2 }).then(canvas => {
                const imgData = canvas.toDataURL('image/png');
                const pdf = new jsPDF({ orientation: 'landscape', unit: 'mm', format: 'a4' });
                const pdfWidth = pdf.internal.pageSize.getWidth();
                const margin = 15;
                pdf.setFont("helvetica", "bold"); pdf.setFontSize(16);
                pdf.text(escolaNome, pdfWidth / 2, margin, { align: 'center' });
                pdf.setFontSize(10); pdf.setFont("helvetica", "normal");
                pdf.text(`Turma: ${turmaNome}`, margin, margin + 20);
                pdf.text(`Aluno: ${alunoNome}`, margin, margin + 27);
                pdf.text(`Data: ${dataGeracao}`, pdfWidth - margin, margin + 20, { align: 'right' });
                pdf.line(margin, margin + 32, pdfWidth - margin, margin + 32);
                const ratio = canvas.width / canvas.height;
                const imgWidth = pdfWidth - (margin * 2);
                const imgHeight = imgWidth / ratio;
                pdf.addImage(imgData, 'PNG', margin, margin + 35, imgWidth, imgHeight);
                pdf.save(`Desempenho_${alunoNome.replace(/ /g, '_')}.pdf`);
                btnGerarPdf.disabled = false;
                btnGerarPdf.querySelector('.text').textContent = 'Gerar PDF';
            });
        });

        carregarTurmas();
    });
    </script>
    @endpush
</x-app-layout>