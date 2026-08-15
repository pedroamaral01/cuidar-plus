<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orientacoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dispositivo_id')->constrained('dispositivos')->cascadeOnDelete();
            $table->string('titulo');
            $table->string('subtitulo')->nullable();
            $table->boolean('publicada')->default(true)->index();
            $table->timestamps();
        });

        // Cada tipo de cuidado é uma aba na tela de detalhe da orientação
        // (ex.: "Troca da bolsa" / "Esvaziamento" / "Higiene").
        Schema::create('tipos_de_cuidado', function (Blueprint $table) {
            $table->id();
            $table->foreignId('orientacao_id')->constrained('orientacoes')->cascadeOnDelete();
            $table->string('nome');
            $table->unsignedSmallInteger('ordem')->default(0);
            $table->timestamps();

            $table->index(['orientacao_id', 'ordem']);
        });

        // Passo a passo numerado exibido dentro da aba selecionada.
        Schema::create('passos_de_orientacao', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tipo_de_cuidado_id')->constrained('tipos_de_cuidado')->cascadeOnDelete();
            $table->text('descricao');
            $table->unsignedSmallInteger('ordem')->default(0);
            $table->timestamps();

            $table->index(['tipo_de_cuidado_id', 'ordem']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('passos_de_orientacao');
        Schema::dropIfExists('tipos_de_cuidado');
        Schema::dropIfExists('orientacoes');
    }
};
