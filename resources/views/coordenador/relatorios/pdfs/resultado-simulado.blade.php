<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Resultado do Simulado</title>
    <style>
        /* Estilos Padrão (como no comparativo) */
        @page { margin: 1.5cm; }
        body { font-family: 'Helvetica Neue', Arial, sans-serif; font-size: 10pt; color: #333; }
        h1 { text-align: center; border-bottom: 1px solid #ccc; padding-bottom: 10px; font-size: 18pt; }
        .header-info { background-color: #f2f2f2; padding: 10px; border-radius: 5px; margin-bottom: 20px; font-size: 10pt; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: center; }
        th { background-color: #f2f2f2; font-weight: bold; }
        td:nth-child(2) { text-align: left; }
    </style>
</head>
<body>
    <header><h1>Resultado do Simulado</h1></header>

    <div class="header-info">
        <strong>Simulado:</strong> {{ $modelo->nome }}<br>
        <strong>Série:</strong> {{ $modelo->serie->nome }} | <strong>Ano Letivo:</strong> {{ $ano_letivo->ano }}
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 10%;">Posição</th>
                <th style="width: 50%;">Aluno</th>
                <th>Nota Final</th>
            </tr>
        </thead>
        <tbody>
            @forelse($resultados as $resultado)
            <tr>
                <td>{{ $loop->iteration }}º</td>
                <td>{{ $resultado->aluno->nome }}</td>
                <td>{{ number_format($resultado->nota, 2, ',', '.') }}</td>
            </tr>
            @empty
            <tr><td colspan="3">Nenhum resultado finalizado encontrado para este simulado.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>