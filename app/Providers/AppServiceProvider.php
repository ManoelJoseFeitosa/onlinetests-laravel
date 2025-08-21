<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Config;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Linha para garantir que a paginação use o estilo do Bootstrap 5
        \Illuminate\Pagination\Paginator::useBootstrapFive();

        // Linha para definir os planos para serem usados em toda a aplicação
        \Illuminate\Support\Facades\Config::set('planos', [
            'essencial' => [
                'display_name' => 'Plano Essencial',
                'questoes' => 1000, 
                'professor' => 10,
                'aluno' => 500,
                'coordenador' => 1,
                'preco' => 'R$ 149,90'
            ],
            'profissional' => [
                'display_name' => 'Plano Profissional',
                'questoes' => 3000,
                'professor' => 100,
                'aluno' => 1000,
                'coordenador' => 4,
                'preco' => 'R$ 249,90'
            ],
            'enterprise' => [
                'display_name' => 'Plano Enterprise',
                'questoes' => INF, 
                'professor' => INF,
                'aluno' => INF,
                'coordenador' => INF,
                'preco' => 'R$ 449,90'
            ]
        ]);
    }
}
