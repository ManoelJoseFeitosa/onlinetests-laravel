<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Relatório de Saúde do Banco de Questões</title>
    <style>
        /* Estilos Padrão (como no comparativo) */
        @page { margin: 1.5cm; }
        body { font-family: 'Helvetica Neue', Arial, sans-serif; font-size: 10pt; color: #333; }
        h1 { text-align: center; border-bottom: 1px solid #ccc; padding-bottom: 10px; font-size: 18pt; }
        h2 { font-size: 14pt; border-bottom: 1px solid #eee; padding-bottom: 5px; margin-top: 30px; }
        .header-info { background-color: #f2f2f2; padding: 10px; border-radius: 5px; margin-bottom: 20px; font-size: 10pt; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; }
        td:last-child { text-align: center; font-weight: bold; }
    </style>
</head>
<body>
    <header><h1>Relatório de Saúde do Banco de Questões</h1></header>

    <div class="header-info">
        <strong>Escola:</strong> {{ $escola_nome }} | <strong>Data:</strong> {{ $data_geracao->format('d/m/Y') }}
    </div>

    <h2>Resumo Geral</h2>
    <p>Total de questões cadastradas no banco: <strong>{{ $total_questoes }}</strong></p>

    <h2>Distribuição por Disciplina</h2>
    <table>
        <thead><tr><th>Disciplina</th><th style="width: 20%; text-align: center;">Nº de Questões</th></tr></thead>
        <tbody>
            @foreach($stats_por_disciplina as $stat)
            <tr><td>{{ $stat->nome }}</td><td>{{ $stat->questoes_count }}</td></tr>
            @endforeach
        </tbody>
    </table>

    <h2>Distribuição por Série</h2>
    <table>
        <thead><tr><th>Série</th><th style="width: 20%; text-align: center;">Nº de Questões</th></tr></thead>
        <tbody>
            @foreach($stats_por_serie as $stat)
            <tr><td>{{ $stat->nome }}</td><td>{{ $stat->questoes_count }}</td></tr>
            @endforeach
        </tbody>
    </table>

    <h2>Distribuição por Nível</h2>
    <table>
        <thead><tr><th>Nível</th><th style="width: 20%; text-align: center;">Nº de Questões</th></tr></thead>
        <tbody>
            @foreach($stats_por_nivel as $stat)
            <tr><td>{{ ucfirst($stat->nivel) }}</td><td>{{ $stat->total }}</td></tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>