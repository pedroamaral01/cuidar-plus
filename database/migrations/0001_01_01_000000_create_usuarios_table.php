<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('usuarios', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('senha');
            $table->string('perfil', 20)->default('paciente')->index();

            // Dados do paciente (nulos para administradores).
            $table->date('data_de_nascimento')->nullable();
            $table->string('cpf', 14)->nullable();
            $table->string('telefone', 20)->nullable();

            // Cuidador/familiar vinculado — passo 1 do cadastro.
            $table->string('cuidador_nome')->nullable();
            $table->string('cuidador_telefone', 20)->nullable();

            $table->boolean('ativo')->default(true);
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        // Tabela de infraestrutura do framework. A coluna `user_id` é gravada
        // diretamente pelo DatabaseSessionHandler do Laravel e não é
        // configurável — por isso mantém o nome nativo, como `notifications`.
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usuarios');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
