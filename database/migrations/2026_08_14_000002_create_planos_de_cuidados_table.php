<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('planos_de_cuidados', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dispositivo_id')->constrained('dispositivos')->cascadeOnDelete();
            $table->string('nome');
            $table->string('descricao')->nullable();
            $table->boolean('ativo')->default(true)->index();
            $table->timestamps();
        });

        // Cada item vira um Lembrete do paciente no passo 3 do cadastro
        // ("revise o plano de cuidados sugerido para o seu dispositivo").
        Schema::create('itens_do_plano_de_cuidados', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plano_de_cuidados_id')->constrained('planos_de_cuidados')->cascadeOnDelete();
            $table->string('titulo');
            $table->string('tipo', 20)->default('outro');
            $table->time('horario');
            $table->unsignedSmallInteger('ordem')->default(0);
            $table->timestamps();

            $table->index(['plano_de_cuidados_id', 'ordem']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('itens_do_plano_de_cuidados');
        Schema::dropIfExists('planos_de_cuidados');
    }
};
