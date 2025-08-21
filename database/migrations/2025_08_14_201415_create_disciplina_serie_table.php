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
    Schema::create('disciplina_serie', function (Blueprint $table) {
        $table->foreignId('disciplina_id')->constrained()->onDelete('cascade');
        $table->foreignId('serie_id')->constrained()->onDelete('cascade');

        // Define as duas colunas como chave primária para evitar duplicatas
        $table->primary(['disciplina_id', 'serie_id']);
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('disciplina_serie');
    }
};
