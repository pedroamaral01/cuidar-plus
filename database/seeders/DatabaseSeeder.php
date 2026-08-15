<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // A ordem importa: dispositivos primeiro, porque orientações, sinais e
        // os vínculos dos pacientes dependem deles.
        $this->call([
            DispositivoSeeder::class,
            OrientacaoSeeder::class,
            SinalDeAlertaSeeder::class,
            ConteudoEducativoSeeder::class,
            UsuarioDeDemonstracaoSeeder::class,
        ]);
    }
}
