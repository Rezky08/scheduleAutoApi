<?php

namespace App\Listener;

use App\Event\AlgenProcess;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class AlgenListener
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
     * @param  AlgenProcess  $event
     * @return void
     */
    public function handle(AlgenProcess $event)
    {
        return "Testing";
    }
}
