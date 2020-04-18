<?php

namespace App\Listener;

use App\Event\StoreResultKelompokDosen;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class StoreResultKelompokDosenListener
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
     * @param  StoreResultKelompokDosen  $event
     * @return void
     */
    public function handle(StoreResultKelompokDosen $event)
    {
        //
    }
}
