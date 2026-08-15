<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sinais_de_alerta', function (Blueprint $table) {
            $table->id();
            // Nulo = sinal geral, exibido para pacientes de qualquer dispositivo.
            $table->foreignId('dispositivo_id')->nullable()->constrained('dispositivos')->nullOnDelete();
            $table->string('nome');
            $table->text('orientacao');
            $table->string('gravidade', 10)->default('media')->index();
            $table->boolean('publicado')->default(true)->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sinais_de_alerta');
    }
};
