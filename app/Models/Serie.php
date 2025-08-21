<?php

namespace App\Models; // <-- CORRIGIDO AQUI

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Serie extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'escola_id',
    ];

    // RELACIONAMENTOS

    public function escola(): BelongsTo
    {
        return $this->belongsTo(Escola::class);
    }

    public function disciplinas(): BelongsToMany
    {
        return $this->belongsToMany(Disciplina::class, 'disciplina_serie');
    }

    public function professores(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'serie_user');
    }

    /**
     * Uma Série tem muitas Questões.
     */
    public function questoes(): HasMany
    {
        return $this->hasMany(Questao::class);
    }
}