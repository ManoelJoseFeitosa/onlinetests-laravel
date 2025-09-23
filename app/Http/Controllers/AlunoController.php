<?php

namespace App\Http\Controllers;

use App\Models\AnoLetivo;
use App\Models\Avaliacao;
use App\Models\ModeloAvaliacao;
use App\Models\Questao;
use App\Models\Resultado;
use App\Models\Resposta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AlunoController extends Controller
{
    /**
     * Lista as avaliações (modelos e recuperações) disponíveis para o aluno.
     */
    public function listarAvaliacoes(): View
    {
        $aluno = Auth::user();
        $ano_letivo_ativo = AnoLetivo::where('escola_id', $aluno->escola_id)->where('status', 'ativo')->first();

        if (!$ano_letivo_ativo || !$aluno->matriculaAtiva()) {
            return view('aluno.avaliacoes.index', ['avaliacoes_disponiveis' => collect()]);
        }
        $serie_id = $aluno->matriculaAtiva()->serie_id;
        $modelos = ModeloAvaliacao::with('criador', 'serie')->where('serie_id', $serie_id)->get();
        $recuperacoes = $aluno->avaliacoesDesignadas()->with('criador', 'serie')->get();
        $resultados = $aluno->resultados()->where('ano_letivo_id', $ano_letivo_ativo->id)->with('avaliacao')->get();
        $mapa_resultados_modelo = $resultados->where('avaliacao.is_dinamica', true)->keyBy('avaliacao.modelo_id');
        $mapa_resultados_estatica = $resultados->where('avaliacao.is_dinamica', false)->keyBy('avaliacao_id');
        $avaliacoes_disponiveis = collect();

        foreach ($modelos as $modelo) {
            $resultado = $mapa_resultados_modelo->get($modelo->id);
            $avaliacoes_disponiveis->push(['objeto' => $modelo, 'tipo_obj' => 'modelo', 'status' => $resultado->status ?? 'Não Iniciada', 'resultado' => $resultado]);
        }
        foreach ($recuperacoes as $recuperacao) {
            $resultado = $mapa_resultados_estatica->get($recuperacao->id);
            $avaliacoes_disponiveis->push(['objeto' => $recuperacao, 'tipo_obj' => 'recuperacao', 'status' => $resultado->status ?? 'Não Iniciada', 'resultado' => $resultado]);
        }
        return view('aluno.avaliacoes.index', ['avaliacoes_disponiveis' => $avaliacoes_disponiveis]);
    }

   /**
     * Gera uma instância de Avaliacao a partir de um Modelo e redireciona para a prova.
     */
    public function iniciarAvaliacaoDinamica(ModeloAvaliacao $modeloAvaliacao)
    {
        $aluno = Auth::user();
        $ano_letivo_ativo = AnoLetivo::where('escola_id', $aluno->escola_id)->where('status', 'ativo')->firstOrFail();

        $questoes_selecionadas_ids = [];
        $regras = $modeloAvaliacao->regras_selecao;
        $regras_disciplinas = $regras['disciplinas'] ?? [];

        foreach ($regras_disciplinas as $regra_disciplina) {
            $assuntos = collect($regra_disciplina['questoes_por_assunto'] ?? [])->pluck('assunto')->toArray();

            foreach ($regra_disciplina['questoes_por_nivel'] ?? [] as $regra_nivel) {
                $quantidade_desejada = $regra_nivel['quantidade'];
                $nivel_desejado = $regra_nivel['nivel'];

                if ($quantidade_desejada == 0) continue;

                // Prepara a consulta base para esta regra
                $query_base = Questao::where('disciplina_id', $regra_disciplina['id'])
                    // CORREÇÃO: Usa whereHas para consultar a relação muitos-para-muitos com Series
                    ->whereHas('series', function ($query) use ($modeloAvaliacao) {
                        $query->where('series.id', $modeloAvaliacao->serie_id);
                    })
                    ->whereNotIn('id', $questoes_selecionadas_ids);
                
                if (!empty($assuntos)) {
                    $query_base->whereIn('assunto', $assuntos);
                }

                // 1. Tenta buscar as questões no nível ideal, de forma aleatória
                $questoes_ideais = (clone $query_base)
                                        ->where('nivel', $nivel_desejado)
                                        ->inRandomOrder()
                                        ->limit($quantidade_desejada)
                                        ->pluck('id');
                
                $questoes_selecionadas_ids = array_merge($questoes_selecionadas_ids, $questoes_ideais->toArray());
                
                $deficit = $quantidade_desejada - $questoes_ideais->count();

                // 3. Se faltarem questões, busca em outros níveis como fallback
                if ($deficit > 0) {
                    $questoes_fallback = (clone $query_base)
                                        ->where('nivel', '!=', $nivel_desejado) 
                                        ->orderByRaw("FIELD(nivel, 'facil', 'media', 'dificil')")
                                        ->limit($deficit)
                                        ->pluck('id');
                    
                    $questoes_selecionadas_ids = array_merge($questoes_selecionadas_ids, $questoes_fallback->toArray());
                }
            }
        }
        
        // Valida se o número total de questões encontradas atende ao total desejado
        $total_desejado = 0;
        foreach($regras_disciplinas as $rd) {
            foreach($rd['questoes_por_nivel'] ?? [] as $rn) {
                $total_desejado += $rn['quantidade'];
            }
        }

        if (count($questoes_selecionadas_ids) < $total_desejado) {
            return redirect()->route('aluno.avaliacoes.index')->with('error', 'Não foi possível gerar a avaliação. Não há questões suficientes no banco que atendam às regras do modelo.');
        }

        $disciplina_id_final = null;
        if (count($regras_disciplinas) === 1) {
            $disciplina_id_final = $regras_disciplinas[0]['id'];
        }

        $avaliacao = DB::transaction(function() use ($modeloAvaliacao, $aluno, $ano_letivo_ativo, $questoes_selecionadas_ids, $disciplina_id_final) {
            $avaliacao = Avaliacao::create([
                'nome' => $modeloAvaliacao->nome, 
                'tipo' => $modeloAvaliacao->tipo, 
                'tempo_limite' => $modeloAvaliacao->tempo_limite, 
                'criador_id' => $modeloAvaliacao->criador_id, 
                'disciplina_id' => $disciplina_id_final,
                'serie_id' => $modeloAvaliacao->serie_id, 
                'escola_id' => $aluno->escola_id, 
                'ano_letivo_id' => $ano_letivo_ativo->id, 
                'is_dinamica' => true, 
                'modelo_id' => $modeloAvaliacao->id,
            ]);
            
            $avaliacao->questoes()->sync($questoes_selecionadas_ids);
            
            $avaliacao->resultados()->create([
                'aluno_id' => $aluno->id, 
                'ano_letivo_id' => $ano_letivo_ativo->id,
                'data_realizacao' => now(), 
                'status' => 'Iniciada'
            ]);
            
            return $avaliacao;
        });

        return redirect()->route('aluno.avaliacoes.responder', $avaliacao);
    }

    /**
     * Mostra a tela para o aluno responder uma avaliação.
     */
    public function responderAvaliacao(Avaliacao $avaliacao): View
    {
        // Garante que o usuário logado só possa acessar avaliações
        // que foram designadas para ele ou que são para a sua turma.
        // (Esta é uma verificação de segurança importante)
        $user = Auth::user();
        $isDesignado = $avaliacao->alunosDesignados->contains($user->id);
        $isDaTurma = $avaliacao->serie_id === $user->matriculaAtiva()?->serie_id;

        if (!$isDesignado && !$isDaTurma) {
            abort(403, 'Acesso não autorizado a esta avaliação.');
        }

        // Carrega as relações necessárias de forma otimizada
        $avaliacao->load(['disciplina', 'questoes.disciplina']);

        return view('aluno.avaliacoes.responder', ['avaliacao' => $avaliacao]);
    }

    /**
     * Salva as respostas do aluno e atualiza o status da avaliação.
     */
    public function salvarRespostas(Request $request, Avaliacao $avaliacao)
{
    $aluno = Auth::user();
    $resultado = Resultado::firstOrCreate(
        ['aluno_id' => $aluno->id, 'avaliacao_id' => $avaliacao->id],
        ['ano_letivo_id' => AnoLetivo::where('escola_id', $aluno->escola_id)->where('status', 'ativo')->firstOrFail()->id, 'data_realizacao' => now(), 'status' => 'Iniciada']
    );

    if ($resultado->status === 'Finalizado') {
        return redirect()->route('dashboard')->with('error', 'Esta avaliação já foi finalizada.');
    }

    $respostas_enviadas = $request->input('respostas', []);

    foreach ($respostas_enviadas as $questao_id => $resposta_aluno) {
        Resposta::updateOrCreate(
            ['resultado_id' => $resultado->id, 'questao_id' => $questao_id],
            ['resposta_aluno' => $resposta_aluno]
        );
    }

    // Define o status como 'Aguardando Correção'. Ele só será 'Finalizado'
    // após a ação do professor/coordenador no CorrecaoController.
    $resultado->status = 'Aguardando Correção';
    $resultado->data_realizacao = now(); // Atualiza a data para o momento do envio
    $resultado->save();

    // Faz a auto-correção das questões objetivas para adiantar o processo
    $pontos_totais = 0;
    $respostas_salvas = $resultado->respostas()->with('questao')->get();

    foreach ($respostas_salvas as $resposta) {
        $questao = $resposta->questao;
        $pontos = 0;
        if ($questao->tipo !== 'discursiva' && $resposta->resposta_aluno === $questao->gabarito) {
            $pontos = 1.0;
        }
        $resposta->pontos = $pontos;
        // Define o status da resposta como 'avaliada' apenas para objetivas
        if ($questao->tipo !== 'discursiva') {
            $resposta->status_correcao = 'avaliada';
        }
        $resposta->save();
        $pontos_totais += $pontos;
    }

    // Salva uma nota parcial (sem as discursivas), normalizada de 0 a 10
    $total_questoes = $avaliacao->questoes()->count();
    $resultado->nota = ($total_questoes > 0) ? ($pontos_totais / $total_questoes) * 10 : 0;
    $resultado->save();

    return redirect()->route('dashboard')->with('success', 'Avaliação enviada com sucesso! Aguardando correção do professor.');
}

public function meusResultados(): View
{
    $aluno = Auth::user();
    $ano_letivo_ativo = AnoLetivo::where('escola_id', $aluno->escola_id)
                                ->where('status', 'ativo')->first();

    if (!$ano_letivo_ativo) {
        return view('aluno.meus-resultados', [
            'dados_por_disciplina' => collect(),
            'stats' => ['total_provas' => 0, 'media_provas' => 0, 'total_simulados' => 0, 'media_simulados' => 0, 'total_recuperacao' => 0, 'media_recuperacao' => 0],
            'chart_data_provas' => ['labels' => [], 'datasets' => []],
            'chart_simulados' => ['labels' => [], 'data' => []],
            'chart_recuperacoes' => ['labels' => [], 'data' => []],
        ]);
    }

    // <--CONSULTA MAIS ROBUSTA PARA FILTRAR DELETADOS -->
    // Usamos um JOIN explícito para garantir que avaliações com soft delete sejam removidas.
    $resultados = Resultado::join('avaliacaos', 'resultados.avaliacao_id', '=', 'avaliacaos.id')
        ->where('resultados.aluno_id', $aluno->id)
        ->where('resultados.ano_letivo_id', $ano_letivo_ativo->id)
        ->where('resultados.status', 'Finalizado')
        ->whereNull('avaliacaos.deleted_at') // A condição explícita do Soft Delete
        ->with('avaliacao.disciplina')
        ->orderBy('resultados.data_realizacao', 'asc')
        ->select('resultados.*') // Evita conflito de colunas (ex: 'id')
        ->get();

    // Separa os resultados por tipo para as estatísticas e os gráficos
    $provas = $resultados->where('avaliacao.tipo', 'prova');
    $simulados = $resultados->where('avaliacao.tipo', 'simulado');
    $recuperacoes = $resultados->where('avaliacao.tipo', 'recuperacao');
    
    // <-- LÓGICA CORRETA PARA DESEMPENHO POR DISCIPLINA -->
    // Agora, baseamos o desempenho por disciplina APENAS nos resultados do tipo 'prova',
    // que são os que devem ter uma disciplina vinculada.
    $dados_por_disciplina = $provas->filter(function ($resultado) {
        return !is_null($resultado->avaliacao->disciplina);
    })->groupBy('avaliacao.disciplina.nome')->map(function ($resultados_do_grupo) {
        return [
            'media' => $resultados_do_grupo->avg('nota'),
            'resultados' => $resultados_do_grupo,
        ];
    });

    // O restante da lógica permanece o mesmo
    $stats = [
        'total_provas' => $provas->count(), 'media_provas' => $provas->avg('nota') ?? 0,
        'total_simulados' => $simulados->count(), 'media_simulados' => $simulados->avg('nota') ?? 0,
        'total_recuperacao' => $recuperacoes->count(), 'media_recuperacao' => $recuperacoes->avg('nota') ?? 0,
    ];

    $chart_data_provas = [
        'labels' => $provas->map(fn($r) => $r->avaliacao->nome . ' (' . $r->data_realizacao->format('d/m') . ')')->toArray(),
        'datasets' => []
    ];
    if ($provas->isNotEmpty()) {
        $chart_data_provas['datasets'][] = [
            'label' => 'Evolução de Notas', 'data' => $provas->pluck('nota')->toArray(),
            'borderColor' => 'rgb(75, 192, 192)', 'backgroundColor' => 'rgba(75, 192, 192, 0.1)',
            'fill' => true, 'tension' => 0.2
        ];
    }
    
    return view('aluno.meus-resultados', [
        'dados_por_disciplina' => $dados_por_disciplina, 
        'stats' => $stats,
        'chart_data_provas' => $chart_data_provas,
        'chart_simulados' => ['labels' => $simulados->pluck('avaliacao.nome'), 'data' => $simulados->pluck('nota')],
        'chart_recuperacoes' => ['labels' => $recuperacoes->pluck('avaliacao.nome'), 'data' => $recuperacoes->pluck('nota')],
    ]);
}

    public function verResultado(Resultado $resultado): View
    {
        if ($resultado->aluno_id !== Auth::id()) {
            abort(403);
        }
        $resultado->load('avaliacao.questoes', 'respostas');
        $respostas_map = $resultado->respostas->keyBy('questao_id');
        return view('aluno.ver-resultado', ['resultado' => $resultado, 'respostas_map' => $respostas_map]);
    }

    public function bloquearProva(Request $request, Avaliacao $avaliacao)
    {
        $aluno = Auth::user();
        // Encontra o resultado para o aluno e a avaliação
        $resultado = Resultado::where('aluno_id', $aluno->id)
                            ->where('avaliacao_id', $avaliacao->id)
                            ->first();

        if ($resultado) {
            $resultado->is_blocked = true;
            $resultado->save();
        }

        return response()->json(['success' => true]);
    }
}
