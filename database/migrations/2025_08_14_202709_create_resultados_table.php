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
    Schema::create('resultados', function (Blueprint $table) {
        $table->id();
        $table->float('nota')->nullable();
        $table->string('status', 50)->default('Pendente');
        $table->dateTime('data_realizacao');
        $table->foreignId('aluno_id')->constrained('users')->onDelete('cascade');
        $table->foreignId('avaliacao_id')->constrained('avaliacaos')->onDelete('cascade');
        $table->foreignId('ano_letivo_id')->nullable()->constrained('ano_letivos')->onDelete('set null');
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resultados');
    }
};
