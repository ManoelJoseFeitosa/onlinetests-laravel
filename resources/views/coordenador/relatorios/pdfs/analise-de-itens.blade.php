<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Relatório de Análise de Itens</title>
    <style>
        @page { margin: 1.5cm; }
        body { font-family: 'Helvetica Neue', Arial, sans-serif; font-size: 9pt; color: #333; }
        h1 { text-align: center; border-bottom: 1px solid #ccc; padding-bottom: 10px; font-size: 18pt; }
        .header-info { background-color: #f2f2f2; padding: 10px; border-radius: 5px; margin-bottom: 20px; font-size: 10pt; }
        .item-card { border: 1px solid #ddd; border-radius: 5px; margin-bottom: 15px; padding: 10px; page-break-inside: avoid; }
        .item-header { font-weight: bold; border-bottom: 1px solid #eee; padding-bottom: 8px; margin-bottom: 8px; }
        .item-texto { font-style: italic; color: #555; }
        .distrator-table { width: 100%; border-collapse: collapse; font-size: 9pt; margin-top: 10px; }
        .distrator-table th, .distrator-table td { border: 1px solid #ddd; padding: 6px; text-align: center; }
        .distrator-table th { background-color: #f2f2f2; }
        .gabarito { background-color: #d4edda; font-weight: bold; }
    </style>
</head>
<body>
    <header>
        <h1>Relatório de Análise de Itens</h1>
    </header>

    <div class="header-info">
        <strong>Modelo:</strong> {{ $modelo->nome }}<br>
        <strong>Série:</strong> {{ $modelo->serie->nome }} | <strong>Ano Letivo:</strong> {{ $ano_letivo->ano }} | <strong>Data:</strong> {{ $data_geracao->format('d/m/Y') }}
    </div>

    @forelse ($analise_data as $item)
        <div class="item-card">
            <div class="item-header">
                <span>Questão ID: {{ $item['questao']->id }} | Assunto: {{ $item['questao']->assunto }}</span>
                <span style="float: right;">Gabarito: {{ $item['questao']->gabarito }}</span>
            </div>
            <p class="item-texto"><strong>Enunciado:</strong> {{ Str::limit(strip_tags($item['questao']->texto), 200) }}</p>

            <p><strong>Índice de Dificuldade:</strong> 
                @php $p_value = ($item['total_respostas'] > 0) ? ($item['total_acertos'] / $item['total_respostas'] * 100) : 0; @endphp
                <strong>{{ number_format($p_value, 1, ',', '.') }}%</strong> 
                <small style="color: #6c757d;">(Taxa de acerto)</small>
            </p>

            <table class="distrator-table">
                <thead>
                    <tr>
                        <th>Alternativa</th><th>A</th><th>B</th><th>C</th><th>D</th><th>Nula/Branco</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>% Escolha</strong></td>
                        @foreach (['A', 'B', 'C', 'D', 'NULA'] as $opt)
                            @php $percentual = ($item['total_respostas'] > 0) ? ($item['distratores'][$opt] / $item['total_respostas'] * 100) : 0; @endphp
                            <td class="{{ $opt == $item['questao']->gabarito ? 'gabarito' : '' }}">{{ number_format($percentual, 1, ',', '.') }}%</td>
                        @endforeach
                    </tr>
                </tbody>
            </table>
        </div>
    @empty
        <p>Nenhum resultado encontrado para gerar a análise de itens.</p>
    @endforelse
</body>
</html>