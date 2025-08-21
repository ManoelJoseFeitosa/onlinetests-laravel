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
    Schema::create('avaliacaos', function (Blueprint $table) {
        $table->id();
        $table->string('nome', 150);
        $table->string('tipo', 20)->default('prova');
        $table->integer('tempo_limite')->nullable();
        $table->boolean('is_dinamica')->default(false);
        $table->foreignId('criador_id')->constrained('users')->onDelete('cascade');
        $table->foreignId('disciplina_id')->nullable()->constrained()->onDelete('set null');
        $table->foreignId('serie_id')->nullable()->constrained()->onDelete('set null');
        $table->foreignId('escola_id')->constrained()->onDelete('cascade');
        $table->foreignId('ano_letivo_id')->nullable()->constrained('ano_letivos')->onDelete('set null');
        $table->foreignId('modelo_id')->nullable()->constrained('modelo_avaliacaos')->onDelete('set null');
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('avaliacaos');
    }
};
