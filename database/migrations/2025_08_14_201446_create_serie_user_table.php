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
    Schema::create('questoes', function (Blueprint $table) {
        $table->id();

        // Chaves estrangeiras que ligam a questão ao seu contexto
        $table->foreignId('disciplina_id')->constrained()->onDelete('cascade');
        $table->foreignId('serie_id')->constrained()->onDelete('cascade');
        $table->foreignId('criador_id')->constrained('users')->onDelete('cascade'); // Liga com a tabela 'users'

        // Conteúdo da questão
        $table->string('assunto', 200);
        $table->string('tipo', 50)->default('multipla_escolha');
        $table->text('texto');
        $table->string('nivel', 10)->default('media');
        $table->string('imagem_nome', 255)->nullable();
        $table->string('imagem_alt', 500)->nullable();

        // Opções para questões de múltipla escolha (podem ser nulas para outros tipos)
        $table->text('opcao_a')->nullable();
        $table->text('opcao_b')->nullable();
        $table->text('opcao_c')->nullable();
        $table->text('opcao_d')->nullable();

        // Resposta e feedback
        $table->string('gabarito', 1)->nullable();
        $table->text('justificativa_gabarito')->nullable();

        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('serie_user');
    }
};
