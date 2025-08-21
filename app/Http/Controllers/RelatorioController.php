<?php

namespace App\Http\Controllers;

use App\Models\AnoLetivo;
use App\Models\Disciplina;
use App\Models\Matricula;
use App\Models\ModeloAvaliacao;
use App\Models\Questao;
use App\Models\Resultado;
use App\Models\Serie;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class RelatorioController extends Controller
{
    public function index(): View
    {
        $escola_id = Auth::user()->escola_id;
        $anos_letivos = AnoLetivo::where('escola_id', $escola_id)->orderBy('ano', 'desc')->get();
        $series = Serie::where('escola_id', $escola_id)->orderBy('nome')->get();
        $disciplinas = Disciplina::where('escola_id', $escola_id)->orderBy('nome')->get();
        $modelos_avaliacao = ModeloAvaliacao::where('escola_id', $escola_id)->orderBy('nome')->get();
        return view('coordenador.relatorios.index', compact('anos_letivos', 'series', 'disciplinas', 'modelos_avaliacao'));
    }

    public function alunosPorSerie(Request $request)
    {
        $data = $request->validate(['ano_letivo_id' => 'required|exists:ano_letivos,id', 'serie_id' => 'required|exists:series,id']);
        $ano_letivo = AnoLetivo::find($data['ano_letivo_id']);
        $serie = Serie::find($data['serie_id']);
        $matriculas = Matricula::with('aluno')->where('serie_id', $data['serie_id'])->where('ano_letivo_id', $data['ano_letivo_id'])->get()->sortBy('aluno.nome');

        $pdf = Pdf::loadView('coordenador.relatorios.pdfs.alunos-por-serie', compact('matriculas', 'serie', 'ano_letivo'));
        return $pdf->stream('lista_alunos_'.$serie->nome.'.pdf');
    }

    public function boletimTurma(Request $request)
    {
        $data = $request->validate(['ano_letivo_id' => 'required|exists:ano_letivos,id', 'serie_id' => 'required|exists:series,id']);
        $ano_letivo = AnoLetivo::find($data['ano_letivo_id']);
        $serie = Serie::find($data['serie_id']);
        $alunos_da_serie = User::whereHas('matriculas', fn($q) => $q->where('serie_id', $data['serie_id'])->where('ano_letivo_id', $data['ano_letivo_id']))
            ->with(['resultados' => fn($q) => $q->where('ano_letivo_id', $data['ano_letivo_id'])->where('status', 'Finalizado')->with('avaliacao.disciplina')])
            ->orderBy('nome')->get();

        $pdf = Pdf::loadView('coordenador.relatorios.pdfs.boletim-turma', compact('alunos_da_serie', 'serie', 'ano_letivo'));
        return $pdf->stream('boletim_'.$serie->nome.'.pdf');
    }

    public function analiseDeItens(Request $request)
    {
        $data = $request->validate(['ano_letivo_id' => 'required|exists:ano_letivos,id', 'modelo_id' => 'required|exists:modelo_avaliacaos,id']);
        $ano_letivo = AnoLetivo::find($data['ano_letivo_id']);
        $modelo = ModeloAvaliacao::with('serie')->find($data['modelo_id']);

        // Lógica para Análise de Itens
        $analise_data = []; // Implementar a lógica de busca e cálculo aqui

        $pdf = Pdf::loadView('coordenador.relatorios.pdfs.analise-de-itens', compact('modelo', 'ano_letivo', 'analise_data'));
        return $pdf->stream('analise_itens_'.$modelo->nome.'.pdf');
    }

    public function desempenhoPorAssunto(Request $request)
    {
    $data = $request->validate([
        'ano_letivo_id' => 'required|exists:ano_letivos,id', 
        'serie_id' => 'required|exists:series,id', 
        'disciplina_id' => 'required|exists:disciplinas,id'
    ]);
    $ano_letivo = AnoLetivo::find($data['ano_letivo_id']);
    $serie = Serie::find($data['serie_id']);
    $disciplina = Disciplina::find($data['disciplina_id']);

    $desempenho_data = DB::table('respostas')
        ->join('questoes', 'respostas.questao_id', '=', 'questoes.id')
        ->join('resultados', 'respostas.resultado_id', '=', 'resultados.id')
        ->where('questoes.disciplina_id', $data['disciplina_id'])
        ->where('resultados.ano_letivo_id', $data['ano_letivo_id'])
        ->whereIn('resultados.aluno_id', function($query) use ($data) {
            $query->select('aluno_id')->from('matriculas')->where('serie_id', $data['serie_id']);
        })
        ->select('questoes.assunto', DB::raw('count(respostas.id) as total_respostas'), DB::raw("sum(case when respostas.resposta_aluno = questoes.gabarito then 1 else 0 end) as total_acertos"))
        ->groupBy('questoes.assunto')->orderBy('questoes.assunto')->get();

    // A linha abaixo foi corrigida
    $pdf = Pdf::loadView('coordenador.relatorios.pdfs.desempenho-por-assunto', [
        'disciplina' => $disciplina,
        'serie' => $serie,
        'ano_letivo' => $ano_letivo,
        'desempenho_data' => $desempenho_data,
        'data_geracao' => now() // <-- Variável adicionada aqui
    ]);
        return $pdf->stream('desempenho_assunto.pdf');
    }

    public function desempenhoPorNivel(Request $request)
    {
    $data = $request->validate([
        'ano_letivo_id' => 'required|exists:ano_letivos,id', 
        'serie_id' => 'required|exists:series,id', 
        'disciplina_id' => 'required|exists:disciplinas,id'
    ]);
    $ano_letivo = AnoLetivo::find($data['ano_letivo_id']);
    $serie = Serie::find($data['serie_id']);
    $disciplina = Disciplina::find($data['disciplina_id']);

    $desempenho_data = DB::table('respostas')
        ->join('questoes', 'respostas.questao_id', '=', 'questoes.id')
        ->join('resultados', 'respostas.resultado_id', '=', 'resultados.id')
        ->where('questoes.disciplina_id', $data['disciplina_id'])
        ->where('resultados.ano_letivo_id', $data['ano_letivo_id'])
        ->whereIn('resultados.aluno_id', function($query) use ($data) {
            $query->select('aluno_id')->from('matriculas')->where('serie_id', $data['serie_id']);
        })
        ->select('questoes.nivel', DB::raw('count(respostas.id) as total_respostas'), DB::raw("sum(case when respostas.resposta_aluno = questoes.gabarito then 1 else 0 end) as total_acertos"))
        ->groupBy('questoes.nivel')->get();

    // A linha abaixo foi corrigida
    $pdf = Pdf::loadView('coordenador.relatorios.pdfs.desempenho-por-nivel', [
        'disciplina' => $disciplina,
        'serie' => $serie,
        'ano_letivo' => $ano_letivo,
        'desempenho_data' => $desempenho_data,
        'data_geracao' => now() // <-- Variável adicionada aqui
    ]);
        return $pdf->stream('desempenho_nivel.pdf');
    }

    public function comparativoTurmas(Request $request)
    {
        $data = $request->validate([
            'ano_letivo_id' => 'required|exists:ano_letivos,id',
            'disciplina_id' => 'required|exists:disciplinas,id'
        ]);

        $ano_letivo = AnoLetivo::find($data['ano_letivo_id']);
        $disciplina = Disciplina::find($data['disciplina_id']);

        // A lógica para buscar os dados (stats_por_turma) continua aqui
        $stats_por_turma = []; // Lembre-se que esta lógica ainda é um placeholder

        // A linha abaixo foi corrigida para incluir a data de geração
        $pdf = Pdf::loadView('coordenador.relatorios.pdfs.comparativo-turmas', [
            'disciplina' => $disciplina,
            'ano_letivo' => $ano_letivo,
            'stats_por_turma' => $stats_por_turma,
            'data_geracao' => now() // <-- Variável que faltava, adicionada aqui
        ]);

        return $pdf->stream('comparativo_turmas.pdf');
    }

    public function saudeBancoQuestoes()
    {
        $escola_id = Auth::user()->escola_id;
        $total_questoes = Questao::whereHas('disciplina', fn($q) => $q->where('escola_id', $escola_id))->count();
        $stats_por_disciplina = Disciplina::where('escola_id', $escola_id)->withCount('questoes')->get();
        $stats_por_serie = Serie::where('escola_id', $escola_id)->withCount('questoes')->get();
        $stats_por_nivel = Questao::whereHas('disciplina', fn($q) => $q->where('escola_id', $escola_id))
                                ->select('nivel', DB::raw('count(*) as total'))->groupBy('nivel')->get();

        $pdf = Pdf::loadView('coordenador.relatorios.pdfs.saude-banco-questoes', [
            'total_questoes' => $total_questoes,
            'stats_por_disciplina' => $stats_por_disciplina,
            'stats_por_serie' => $stats_por_serie,
            'stats_por_nivel' => $stats_por_nivel,
            'escola_nome' => Auth::user()->escola->nome,
            'data_geracao' => now()
        ]);
        return $pdf->stream('saude_banco_questoes.pdf');
    }

    public function listaProfessores()
    {
        $professores = User::where('escola_id', Auth::user()->escola_id)->where('role', 'professor')
                           ->with('disciplinasLecionadas', 'seriesLecionadas')->orderBy('nome')->get();

        $pdf = Pdf::loadView('coordenador.relatorios.pdfs.lista-professores', [
            'professores' => $professores,
            'escola_nome' => Auth::user()->escola->nome,
            'data_geracao' => now()
        ]);
        return $pdf->stream('lista_professores.pdf');
    }

    public function resultadoSimulado(Request $request)
    {
        $data = $request->validate(['ano_letivo_id' => 'required|exists:ano_letivos,id', 'modelo_id' => 'required|exists:modelo_avaliacaos,id']);
        $ano_letivo = AnoLetivo::find($data['ano_letivo_id']);
        $modelo = ModeloAvaliacao::with('serie')->find($data['modelo_id']);
        $resultados = Resultado::where('ano_letivo_id', $data['ano_letivo_id'])
            ->whereHas('avaliacao', fn($q) => $q->where('modelo_id', $data['modelo_id']))
            ->with('aluno')->orderBy('nota', 'desc')->get();

        $pdf = Pdf::loadView('coordenador.relatorios.pdfs.resultado-simulado', compact('modelo', 'ano_letivo', 'resultados'));
        return $pdf->stream('resultado_simulado_'.$modelo->nome.'.pdf');
    }
}