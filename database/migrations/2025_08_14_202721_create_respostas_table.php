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
    Schema::create('respostas', function (Blueprint $table) {
        $table->id();
        $table->foreignId('resultado_id')->constrained()->onDelete('cascade');
        $table->foreignId('questao_id')->constrained('questoes')->onDelete('cascade');
        $table->text('resposta_aluno')->nullable();
        $table->string('status_correcao', 20)->default('nao_avaliada');
        $table->float('pontos')->default(0.0);
        $table->text('feedback_professor')->nullable();
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('respostas');
    }
};
