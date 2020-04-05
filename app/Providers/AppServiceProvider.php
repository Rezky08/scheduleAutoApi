<?php

namespace App\Providers;

use App\Jadwal;
use App\KelompokDosen;
use App\Matakuliah;
use App\Observers\GlobalObserver;
use App\Peminat;
use App\ProcessItem;
use App\ProcessLog;
use App\ProgramStudi;
use App\SemesterDetail;
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
        Jadwal::observe(GlobalObserver::class);
        KelompokDosen::observe(GlobalObserver::class);
        ProcessItem::observe(GlobalObserver::class);
        ProcessLog::observe(GlobalObserver::class);
        SemesterDetail::observe(GlobalObserver::class);
    }
}
