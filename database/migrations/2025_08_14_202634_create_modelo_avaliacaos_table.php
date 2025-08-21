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
    Schema::create('modelo_avaliacaos', function (Blueprint $table) {
        $table->id();
        $table->string('nome', 150);
        $table->string('tipo', 20);
        $table->integer('tempo_limite')->nullable();
        $table->foreignId('criador_id')->constrained('users')->onDelete('cascade');
        $table->foreignId('serie_id')->constrained()->onDelete('cascade');
        $table->foreignId('escola_id')->constrained()->onDelete('cascade');
        $table->json('regras_selecao');
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('modelo_avaliacaos');
    }
};
