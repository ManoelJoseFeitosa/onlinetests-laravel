<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Avaliacao extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'avaliacaos';

    protected $fillable = [
        'nome',
        'tipo',
        'tempo_limite',
        'is_dinamica',
        'criador_id',
        'disciplina_id',
        'serie_id',
        'escola_id',
        'ano_letivo_id',
        'modelo_id',
    ];

    public function criador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'criador_id');
    }

    public function disciplina(): BelongsTo
    {
        return $this->belongsTo(Disciplina::class);
    }

    public function serie(): BelongsTo
    {
        return $this->belongsTo(Serie::class);
    }

    public function escola(): BelongsTo
    {
        return $this->belongsTo(Escola::class);
    }

    public function anoLetivo(): BelongsTo
    {
        return $this->belongsTo(AnoLetivo::class);
    }

    public function modelo(): BelongsTo
    {
        return $this->belongsTo(ModeloAvaliacao::class, 'modelo_id');
    }

    public function alunosDesignados(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'avaliacao_user');
    }

    public function questoes(): BelongsToMany
    {
        return $this->belongsToMany(Questao::class, 'avaliacao_questao');
    }

    public function resultados(): HasMany
    {
        return $this->hasMany(Resultado::class);
    }
}