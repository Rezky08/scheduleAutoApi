<?php

namespace App\Providers;

use App\Matakuliah;
use App\Observers\GlobalObserver;
use App\Peminat;
use App\ProgramStudi;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        ProgramStudi::observe(GlobalObserver::class);
        Matakuliah::observe(GlobalObserver::class);
        Peminat::observe(GlobalObserver::class);
    }
}
