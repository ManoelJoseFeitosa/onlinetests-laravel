<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;
    protected $fillable = [ 'nome', 'email', 'password', 'role', 'precisa_trocar_senha', 'data_aceite_termos', 'escola_id', 'is_superadmin', ];
    protected $hidden = [ 'password', 'remember_token', ];
    protected function casts(): array
    {
        return [ 'email_verified_at' => 'datetime', 'password' => 'hashed', 'precisa_trocar_senha' => 'boolean', 'is_superadmin' => 'boolean', 'data_aceite_termos' => 'datetime', ];
    }
    public function escola(): BelongsTo { return $this->belongsTo(Escola::class); }
    public function avaliacoesCriadas(): HasMany { return $this->hasMany(Avaliacao::class, 'criador_id'); }
    public function resultados(): HasMany { return $this->hasMany(Resultado::class, 'aluno_id'); }
    public function disciplinasLecionadas(): BelongsToMany { return $this->belongsToMany(Disciplina::class, 'disciplina_user'); }
    public function seriesLecionadas(): BelongsToMany { return $this->belongsToMany(Serie::class, 'serie_user'); }
    public function matriculas(): HasMany { return $this->hasMany(Matricula::class, 'aluno_id'); }
    public function avaliacoesDesignadas(): BelongsToMany { return $this->belongsToMany(Avaliacao::class, 'avaliacao_user'); }
    public function matriculaAtiva()
    {
        $ano_letivo_ativo = AnoLetivo::where('escola_id', $this->escola_id)->where('status', 'ativo')->first();
        if (!$ano_letivo_ativo) { return null; }
        return $this->matriculas()->where('ano_letivo_id', $ano_letivo_ativo->id)->with('serie')->first();
    }
}