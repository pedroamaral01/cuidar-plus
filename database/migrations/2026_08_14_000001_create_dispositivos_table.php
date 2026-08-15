<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dispositivos', function (Blueprint $table) {
            $table->id();
            $table->string('nome')->unique();
            $table->string('descricao')->nullable();
            $table->string('icone', 40)->default('Circle');
            $table->string('cor', 20)->default('teal');
            $table->boolean('ativo')->default(true)->index();
            $table->timestamps();
        });

        // Vínculo entre paciente e dispositivo. É histórico: o paciente pode
        // trocar de dispositivo, e o vínculo anterior fica com ativo = false.
        Schema::create('usuarios_dispositivos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('usuarios')->cascadeOnDelete();
            $table->foreignId('dispositivo_id')->constrained('dispositivos')->cascadeOnDelete();
            $table->date('data_de_inicio')->nullable();
            $table->boolean('ativo')->default(true);
            $table->timestamps();

            $table->unique(['usuario_id', 'dispositivo_id']);
            $table->index(['usuario_id', 'ativo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usuarios_dispositivos');
        Schema::dropIfExists('dispositivos');
    }
};
