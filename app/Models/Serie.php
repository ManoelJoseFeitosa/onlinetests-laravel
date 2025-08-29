<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Serie extends Model
{
    use HasFactory;
    protected $fillable = ['nome', 'escola_id'];

    public function escola(): BelongsTo { return $this->belongsTo(Escola::class); }
    public function disciplinas(): BelongsToMany { return $this->belongsToMany(Disciplina::class, 'disciplina_serie'); }
    public function professores(): BelongsToMany { return $this->belongsToMany(User::class, 'serie_user'); }

    // CORREÇÃO: Mudamos a relação para belongsToMany (Muitos-para-Muitos)
    public function questoes(): BelongsToMany
    {
        return $this->belongsToMany(Questao::class, 'questao_serie');
    }
}