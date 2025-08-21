<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Resultado extends Model
{
    use HasFactory;

    protected $fillable = [
        'nota',
        'status',
        'data_realizacao',
        'aluno_id',
        'avaliacao_id',
        'ano_letivo_id',
    ];

    protected $casts = [
        'data_realizacao' => 'datetime',
    ];

    public function aluno(): BelongsTo
    {
        return $this->belongsTo(User::class, 'aluno_id');
    }

    public function avaliacao(): BelongsTo
    {
        return $this->belongsTo(Avaliacao::class);
    }

    public function anoLetivo(): BelongsTo
    {
        return $this->belongsTo(AnoLetivo::class);
    }

    public function respostas(): HasMany
    {
        return $this->hasMany(Resposta::class);
    }
}