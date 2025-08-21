<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Escola extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nome',
        'cnpj',
        'status',
        'plano',
        'media_recuperacao',
    ];

    // RELACIONAMENTOS

    public function anosLetivos(): HasMany
    {
        return $this->hasMany(AnoLetivo::class);
    }

    public function series(): HasMany
    {
        return $this->hasMany(Serie::class);
    }

    public function disciplinas(): HasMany
    {
        return $this->hasMany(Disciplina::class);
    }

    public function usuarios(): HasMany
    {
        return $this->hasMany(User::class);
    }
}