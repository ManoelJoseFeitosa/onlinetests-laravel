<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Relatório de Desempenho por Nível</title>
    <style>
        /* Estilos Padrão (como no comparativo) */
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
    <header><h1>Relatório de Desempenho por Nível</h1></header>

    <div class="header-info">
        <strong>Disciplina:</strong> {{ $disciplina->nome }} | <strong>Série:</strong> {{ $serie->nome }}<br>
        <strong>Ano Letivo:</strong> {{ $ano_letivo->ano }} | <strong>Data:</strong> {{ $data_geracao->format('d/m/Y') }}
    </div>

    <table>
        <thead>
            <tr>
                <th>Nível de Dificuldade</th>
                <th>Total de Respostas</th>
                <th>Total de Acertos</th>
                <th>% de Acerto</th>
            </tr>
        </thead>
        <tbody>
            @forelse($desempenho_data as $item)
            <tr>
                <td>{{ ucfirst($item->nivel) }}</td>
                <td>{{ $item->total_respostas }}</td>
                <td>{{ $item->total_acertos }}</td>
                <td>{{ $item->total_respostas > 0 ? number_format(($item->total_acertos / $item->total_respostas) * 100, 1, ',', '.') . '%' : 'N/A' }}</td>
            </tr>
            @empty
            <tr><td colspan="4">Nenhum dado encontrado para gerar o relatório.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>