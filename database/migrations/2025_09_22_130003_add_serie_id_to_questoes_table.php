<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('questoes', function (Blueprint $table) {
            // Adiciona a coluna 'serie_id' que pode ser nula
            // e cria a chave estrangeira para a tabela 'series'
            $table->foreignId('serie_id')
                  ->nullable()
                  ->after('disciplina_id') // Opcional: posiciona a coluna no DB
                  ->constrained('series')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('questoes', function (Blueprint $table) {
            // Remove a chave estrangeira e a coluna
            $table->dropForeign(['serie_id']);
            $table->dropColumn('serie_id');
        });
    }
};