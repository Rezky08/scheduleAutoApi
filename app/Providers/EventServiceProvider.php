<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        'App\Event\AlgenKelompokDosenProcess' => [
            'App\Listener\AlgenKelompokDosenListener'
        ],
        'App\Event\GetMataKuliahKelompok' => [
            'App\Listener\GetMataKuliahKelompokListener'
        ],
        'App\Event\StoreResultKelompokDosen' => [
            'App\Listener\StoreResultKelompokDosenListener'
        ],
        'App\Event\AlgenJadwalProcess' => [
            'App\Listener\AlgenJadwalListener'
        ],
        'App\Event\StoreResultJadwal' => [
            'App\Listener\StoreResultJadwalListener'
        ]
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
