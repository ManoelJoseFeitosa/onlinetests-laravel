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
    Schema::create('users', function (Blueprint $table) {
        $table->id();
        $table->string('nome', 150); // Seu campo 'nome'
        $table->string('email', 150)->unique();
        $table->string('password', 256);
        $table->string('role', 50)->default('aluno'); // Seu campo 'role'
        $table->boolean('precisa_trocar_senha')->default(true); // Seu campo
        $table->timestamp('data_aceite_termos')->nullable(); // Seu campo

        // Chave estrangeira para a tabela escolas
        $table->foreignId('escola_id')->nullable()->constrained('escolas')->onDelete('set null');

        $table->boolean('is_superadmin')->default(false); // Seu campo
        $table->rememberToken(); // Padrão do Laravel
        $table->timestamps(); // Cria created_at e updated_at
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
