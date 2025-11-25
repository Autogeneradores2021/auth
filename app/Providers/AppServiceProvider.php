<?php

namespace App\Providers;

use App\Providers\Registrars\RepositoryRegister;
use App\Providers\Registrars\ServiceRegister;
use App\Providers\Registrars\UtilRegister;
use BMCLibrary\UnitOfWork\UnitOfWork;
use BMCLibrary\UnitOfWork\UnitOfWorkInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        RepositoryRegister::register($this->app);
        UtilRegister::register($this->app);
        ServiceRegister::register($this->app);

        $this->app->bind(
            UnitOfWorkInterface::class,
            UnitOfWork::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
