<?php

namespace App\Http\Controllers;

use App\Models\Resultado;
use App\Models\Resposta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CorrecaoController extends Controller
{
    /**
     * Mostra a tela de correção para um resultado específico.
     */
    public function show(Resultado $resultado): View
    {
        // Garante que o coordenador só acesse resultados da sua escola
        if ($resultado->aluno->escola_id !== Auth::user()->escola_id) {
            abort(403);
        }

        // Carrega todos os dados necessários de forma otimizada
        $resultado->load('aluno', 'avaliacao.questoes', 'respostas');

        // Cria um mapa de respostas para facilitar o acesso na view
        $respostas_map = $resultado->respostas->keyBy('questao_id');

        return view('coordenador.correcao.show', [
            'resultado' => $resultado,
            'respostas_map' => $respostas_map
        ]);
    }

    /**
     * Salva as correções feitas pelo professor/coordenador.
     */
    public function store(Request $request, Resultado $resultado)
    {
    if ($resultado->aluno->escola_id !== Auth::user()->escola_id) {
        abort(403);
    }

    $correcoes = $request->input('correcoes', []);

    DB::transaction(function () use ($correcoes, $resultado) {
        // Itera sobre TODAS as respostas do resultado
        foreach ($resultado->respostas as $resposta) {
            // Verifica se existe uma correção enviada para esta resposta
            if (isset($correcoes[$resposta->questao_id])) {
                $dadosCorrecao = $correcoes[$resposta->questao_id];

                $resposta->feedback_professor = $dadosCorrecao['feedback'] ?? null;

                // Se for discursiva, atualiza o status e os pontos
                if ($resposta->questao->tipo === 'discursiva' && isset($dadosCorrecao['status'])) {
                    $resposta->status_correcao = $dadosCorrecao['status'];
                    $resposta->pontos = $this->getPontosFromStatus($dadosCorrecao['status']);
                }
                $resposta->save();
            }
        }
    });

    // Recalcula a nota final com base em todos os pontos salvos
    $pontos_totais = $resultado->respostas()->sum('pontos');
    $total_questoes = $resultado->avaliacao->questoes()->count();

    $resultado->nota = ($total_questoes > 0) ? ($pontos_totais / $total_questoes) * 10 : 0;
    $resultado->status = 'Finalizado';
    $resultado->save();

    $redirectRoute = Auth::user()->role === 'coordenador' ? 'coordenador.modelos.show' : 'professor.avaliacoes.show';
    $modelo_id = $resultado->avaliacao->modelo_id;

    if (!$modelo_id) { // Fallback para recuperações
        return redirect()->route('dashboard')->with('success', 'Avaliação (Recuperação) corrigida com sucesso!');
    }

    return redirect()->route($redirectRoute, $modelo_id)->with('success', 'Avaliação corrigida e finalizada com sucesso!');
    }

    /**
     * Helper para converter o status da correção em pontos.
     */
    private function getPontosFromStatus(string $status): float
    {
        return match ($status) {
            'correta' => 1.0,
            'parcial' => 0.5,
            default => 0.0,
        };
    }
}