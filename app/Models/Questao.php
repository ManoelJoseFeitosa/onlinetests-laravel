<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Questao extends Model
{
    use HasFactory;

    protected $table = 'questoes';

    protected $fillable = [
        'disciplina_id',
        'serie_id',
        'criador_id',
        'assunto',
        'tipo',
        'texto',
        'nivel',
        'imagem_nome',
        'imagem_alt',
        'opcao_a',
        'opcao_b',
        'opcao_c',
        'opcao_d',
        'gabarito',
        'justificativa_gabarito',
    ];

    public function disciplina(): BelongsTo
    {
        return $this->belongsTo(Disciplina::class);
    }

    public function serie(): BelongsTo
    {
        return $this->belongsTo(Serie::class);
    }

    public function criador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'criador_id');
    }

    public function avaliacoes(): BelongsToMany
    {
        return $this->belongsToMany(Avaliacao::class, 'avaliacao_questao');
    }
}