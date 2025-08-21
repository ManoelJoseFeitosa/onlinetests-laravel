<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Relatório Comparativo por Disciplina</title>
    <style>
        @page { margin: 1.5cm; }
        body { font-family: 'Helvetica Neue', Arial, sans-serif; font-size: 10pt; color: #333; }
        h1 { text-align: center; border-bottom: 1px solid #ccc; padding-bottom: 10px; font-size: 18pt; }
        .header-info { background-color: #f2f2f2; padding: 10px; border-radius: 5px; margin-bottom: 20px; font-size: 10pt; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: center; }
        th { background-color: #f2f2f2; font-weight: bold; }
        td:first-child { text-align: left; }
    </style>
</head>
<body>
    <header><h1>Relatório Comparativo por Disciplina</h1></header>

    <div class="header-info">
        <strong>Disciplina:</strong> {{ $disciplina->nome }}<br>
        <strong>Ano Letivo:</strong> {{ $ano_letivo->ano }} | <strong>Data:</strong> {{ $data_geracao->format('d/m/Y') }}
    </div>

    <table>
        <thead>
            <tr>
                <th>Turma</th>
                <th>Nº de Participantes</th>
                <th>Média Geral</th>
                <th>Maior Nota</th>
                <th>Menor Nota</th>
            </tr>
        </thead>
        <tbody>
            @forelse($stats_por_turma as $stat)
            <tr>
                <td>{{ $stat->nome }}</td>
                <td>{{ $stat->num_participantes }}</td>
                <td>{{ number_format($stat->media_notas, 2, ',', '.') }}</td>
                <td>{{ number_format($stat->maior_nota, 2, ',', '.') }}</td>
                <td>{{ number_format($stat->menor_nota, 2, ',', '.') }}</td>
            </tr>
            @empty
            <tr><td colspan="5">Nenhum dado encontrado para gerar o relatório.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>