<?php

namespace Database\Seeders;

use App\Models\Documento;
use Illuminate\Database\Seeder;

class DocumentoSeeder extends Seeder
{
    public function run(): void
    {
        Documento::create([
            'titulo' => 'Manual do Coordenador (v1.0)',
            'descricao' => 'Guia completo com todas as funcionalidades do painel do coordenador.',
            'caminho_arquivo' => 'manual-coordenador.pdf',
            'ativo' => true,
        ]);
    }
}