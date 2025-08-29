<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Cria a nova tabela-pivô para ligar questões e séries
        Schema::create('questao_serie', function (Blueprint $table) {
            $table->foreignId('questao_id')->constrained('questoes')->onDelete('cascade');
            $table->foreignId('serie_id')->constrained('series')->onDelete('cascade');
            $table->primary(['questao_id', 'serie_id']);
        });

        // 2. Transfere os dados existentes para a nova tabela-pivô
        // (Isso garante que você não perca os dados de questões já criadas)
        $questoes = DB::table('questoes')->whereNotNull('serie_id')->get();
        foreach ($questoes as $questao) {
            DB::table('questao_serie')->insert([
                'questao_id' => $questao->id,
                'serie_id' => $questao->serie_id,
            ]);
        }

        // 3. Remove a coluna 'serie_id' da tabela 'questoes'
        Schema::table('questoes', function (Blueprint $table) {
            $table->dropForeign(['serie_id']); // Remove a chave estrangeira primeiro
            $table->dropColumn('serie_id');
        });
    }

    public function down(): void
    {
        // O processo reverso
        Schema::table('questoes', function (Blueprint $table) {
            $table->foreignId('serie_id')->nullable()->constrained('series');
        });
        // A transferência de dados no down() é complexa e pode ser omitida.
        Schema::dropIfExists('questao_serie');
    }
};