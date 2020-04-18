<?php

namespace App\Listener;

use App\Event\StoreResultJadwal;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class StoreResultJadwalListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  StoreResultJadwal  $event
     * @return void
     */
    public function handle(StoreResultJadwal $event)
    {
        //
    }
}
