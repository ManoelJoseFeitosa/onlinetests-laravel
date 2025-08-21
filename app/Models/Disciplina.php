<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Disciplina extends Model
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

    public function series(): BelongsToMany
    {
        return $this->belongsToMany(Serie::class, 'disciplina_serie');
    }

    public function professores(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'disciplina_user');
    }

    public function questoes(): HasMany
    {
        return $this->hasMany(Questao::class);
    }
}