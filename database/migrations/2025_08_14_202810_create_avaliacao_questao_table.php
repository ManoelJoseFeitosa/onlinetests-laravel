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
    Schema::create('avaliacao_questao', function (Blueprint $table) {
        $table->foreignId('avaliacao_id')->constrained('avaliacaos')->onDelete('cascade');
        $table->foreignId('questao_id')->constrained('questoes')->onDelete('cascade');
        $table->primary(['avaliacao_id', 'questao_id']);
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('avaliacao_questao');
    }
};
