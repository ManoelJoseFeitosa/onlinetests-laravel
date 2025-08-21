<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Boletim da Turma</title>
    <style>
        @page { margin: 1.5cm; }
        body { font-family: 'Helvetica Neue', Arial, sans-serif; font-size: 10pt; color: #333; }
        .boletim-container { page-break-after: always; }
        .boletim-container:last-child { page-break-after: avoid; }
        h1 { text-align: center; border-bottom: 1px solid #ccc; padding-bottom: 10px; font-size: 18pt; }
        .header-info { background-color: #f2f2f2; padding: 10px; border-radius: 5px; margin-bottom: 20px; font-size: 10pt; }
        .disciplina-block { margin-bottom: 20px; }
        .disciplina-header { font-size: 12pt; font-weight: bold; color: #fff; background-color: #0d6efd; padding: 8px; border-radius: 5px 5px 0 0;}
        .disciplina-table { width: 100%; border-collapse: collapse; font-size: 10pt; }
        .disciplina-table th, .disciplina-table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        .disciplina-table th { background-color: #f2f2f2; }
        .disciplina-table tr.media-final { font-weight: bold; background-color: #e9ecef; }
        .disciplina-table td.nota { text-align: right; width: 20%; }
    </style>
</head>
<body>
    @forelse ($alunos_da_serie as $aluno)
    <div class="boletim-container">
        <header>
            <h1>Boletim de Desempenho do Aluno</h1>
        </header>

        <div class="header-info">
            <strong>Aluno(a):</strong> {{ $aluno->nome }}<br>
            <strong>Escola:</strong> {{ $aluno->escola->nome }}<br>
            <strong>Série:</strong> {{ $serie->nome }} | <strong>Ano Letivo:</strong> {{ $ano_letivo->ano }}
        </div>

        @php
            $resultadosAgrupados = $aluno->resultados->groupBy('avaliacao.disciplina.nome');
        @endphp

        @forelse ($resultadosAgrupados as $disciplinaNome => $resultados)
        <div class="disciplina-block">
            <div class="disciplina-header">{{ $disciplinaNome }}</div>
            <table class="disciplina-table">
                <thead>
                    <tr>
                        <th>Avaliação</th>
                        <th class="nota">Nota</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($resultados as $resultado)
                    <tr>
                        <td>{{ $resultado->avaliacao->nome }}</td>
                        <td class="nota">{{ number_format($resultado->nota, 2, ',', '.') }}</td>
                    </tr>
                    @endforeach
                    <tr class="media-final">
                        <td><strong>MÉDIA FINAL</strong></td>
                        <td class="nota"><strong>{{ number_format($resultados->avg('nota'), 2, ',', '.') }}</strong></td>
                    </tr>
                </tbody>
            </table>
        </div>
        @empty
        <p style="text-align: center;">Nenhum resultado finalizado encontrado para este aluno.</p>
        @endforelse
    </div>
    @empty
        <h1>Nenhum Aluno Encontrado</h1>
        <p style="text-align: center;">Não foram encontrados alunos na série <strong>{{ $serie->nome }}</strong> para o ano letivo de <strong>{{ $ano_letivo->ano }}</strong>.</p>
    @endforelse
</body>
</html>