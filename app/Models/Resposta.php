<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Resposta extends Model
{
    use HasFactory;

    protected $fillable = [
        'resultado_id',
        'questao_id',
        'resposta_aluno',
        'status_correcao',
        'pontos',
        'feedback_professor',
    ];

    public function resultado(): BelongsTo
    {
        return $this->belongsTo(Resultado::class);
    }

    public function questao(): BelongsTo
    {
        return $this->belongsTo(Questao::class);
    }
}