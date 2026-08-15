<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('conteudos_educativos', function (Blueprint $table) {
            $table->id();
            // Nulo = conteúdo geral, visível para pacientes de qualquer dispositivo.
            $table->foreignId('dispositivo_id')->nullable()->constrained('dispositivos')->nullOnDelete();
            $table->string('titulo');
            $table->string('resumo')->nullable();
            $table->longText('corpo')->nullable();
            $table->string('tipo', 10)->default('texto')->index();
            $table->string('url_do_video')->nullable();
            $table->boolean('publicado')->default(true)->index();
            $table->timestamps();
        });

        // Favoritos do paciente (tabela pivô simples).
        Schema::create('usuarios_conteudos_favoritos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('usuarios')->cascadeOnDelete();
            $table->foreignId('conteudo_educativo_id')->constrained('conteudos_educativos')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['usuario_id', 'conteudo_educativo_id'], 'usuarios_conteudos_favoritos_unico');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usuarios_conteudos_favoritos');
        Schema::dropIfExists('conteudos_educativos');
    }
};
