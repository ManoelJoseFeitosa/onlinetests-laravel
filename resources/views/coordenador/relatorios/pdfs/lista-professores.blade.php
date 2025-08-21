<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Relatório de Professores</title>
    <style>
        /* Estilos Padrão (como no comparativo) */
        @page { margin: 1.5cm; }
        body { font-family: 'Helvetica Neue', Arial, sans-serif; font-size: 10pt; color: #333; }
        h1 { text-align: center; border-bottom: 1px solid #ccc; padding-bottom: 10px; font-size: 18pt; }
        .header-info { background-color: #f2f2f2; padding: 10px; border-radius: 5px; margin-bottom: 20px; font-size: 10pt; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; }
    </style>
</head>
<body>
    <header><h1>Relatório de Professores</h1></header>

    <div class="header-info">
        <strong>Escola:</strong> {{ $escola_nome }} | <strong>Data:</strong> {{ $data_geracao->format('d/m/Y') }}
    </div>

    <table>
        <thead>
            <tr>
                <th>Professor</th>
                <th>E-mail</th>
                <th>Disciplinas</th>
                <th>Turmas</th>
            </tr>
        </thead>
        <tbody>
            @forelse($professores as $professor)
            <tr>
                <td>{{ $professor->nome }}</td>
                <td>{{ $professor->email }}</td>
                <td>{{ $professor->disciplinasLecionadas->pluck('nome')->join(', ') }}</td>
                <td>{{ $professor->seriesLecionadas->pluck('nome')->join(', ') }}</td>
            </tr>
            @empty
            <tr><td colspan="4" style="text-align: center;">Nenhum professor encontrado.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>