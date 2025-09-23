<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ModeloAvaliacao extends Model
{
    use HasFactory;

    protected $table = 'modelo_avaliacaos';

    protected $fillable = [
        'nome',
        'tipo',
        'tempo_limite',
        'criador_id',
        'serie_id',
        'escola_id',
        'regras_selecao',
    ];

    protected $casts = [
        'regras_selecao' => 'array', // Converte o campo JSON em array automaticamente
    ];

    public function criador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'criador_id');
    }

    public function serie(): BelongsTo
    {
        return $this->belongsTo(Serie::class);
    }

    public function escola(): BelongsTo
    {
        return $this->belongsTo(Escola::class);
    }

    public function avaliacoesGeradas(): HasMany
    {
        return $this->hasMany(Avaliacao::class, 'modelo_id');
    }
}
