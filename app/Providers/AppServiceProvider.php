<?php

namespace App\Providers;

use App\Models\Lembrete;
use App\Models\RegistroDeCuidado;
use App\Policies\LembretePolicy;
use App\Policies\RegistroDeCuidadoPolicy;
use App\Repositories\Contracts\ConteudoEducativoRepositoryInterface;
use App\Repositories\Contracts\LembreteRepositoryInterface;
use App\Repositories\Contracts\OrientacaoRepositoryInterface;
use App\Repositories\Contracts\RegistroDeCuidadoRepositoryInterface;
use App\Repositories\Contracts\SinalDeAlertaRepositoryInterface;
use App\Repositories\Eloquent\ConteudoEducativoRepository;
use App\Repositories\Eloquent\LembreteRepository;
use App\Repositories\Eloquent\OrientacaoRepository;
use App\Repositories\Eloquent\RegistroDeCuidadoRepository;
use App\Repositories\Eloquent\SinalDeAlertaRepository;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Interface => implementação. Os Services dependem sempre da interface,
     * nunca do Eloquent diretamente (Dependency Inversion).
     *
     * @var array<class-string, class-string>
     */
    private const REPOSITORIOS = [
        LembreteRepositoryInterface::class => LembreteRepository::class,
        RegistroDeCuidadoRepositoryInterface::class => RegistroDeCuidadoRepository::class,
        OrientacaoRepositoryInterface::class => OrientacaoRepository::class,
        SinalDeAlertaRepositoryInterface::class => SinalDeAlertaRepository::class,
        ConteudoEducativoRepositoryInterface::class => ConteudoEducativoRepository::class,
    ];

    public function register(): void
    {
        foreach (self::REPOSITORIOS as $contrato => $implementacao) {
            $this->app->bind($contrato, $implementacao);
        }
    }

    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);

        Gate::policy(Lembrete::class, LembretePolicy::class);
        Gate::policy(RegistroDeCuidado::class, RegistroDeCuidadoPolicy::class);
    }
}
