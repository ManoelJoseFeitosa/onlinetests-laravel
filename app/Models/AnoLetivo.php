<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AnoLetivo extends Model
{
    use HasFactory;

    // Nome da tabela, pois o Laravel pluralizaria para "ano_letivos"
    protected $table = 'ano_letivos';

    protected $fillable = [
        'ano',
        'status',
        'escola_id',
    ];

    public function escola(): BelongsTo
    {
        return $this->belongsTo(Escola::class);
    }

    public function matriculas(): HasMany
    {
        return $this->hasMany(Matricula::class);
    }

    public function avaliacoes(): HasMany
    {
        return $this->hasMany(Avaliacao::class);
    }

    public function resultados(): HasMany
    {
        return $this->hasMany(Resultado::class);
    }
}