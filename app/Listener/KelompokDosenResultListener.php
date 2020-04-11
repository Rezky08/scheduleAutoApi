<?php

namespace App\Listener;

use App\Event\CatchKelompokDosenResult;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Http\Request;
use Exception;

class KelompokDosenResultListener implements ShouldQueue
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
     * @param  CatchKelompokDosenResult  $event
     * @return void
     */
    public function handle(CatchKelompokDosenResult $event)
    {
        $kelompok_dosen_results = json_decode($event->request['results']);
        foreach ($kelompok_dosen_results as $key => $kelompok_dosen) {
            foreach ($kelompok_dosen as $key => $item) {
                $request = new Request();
            }
        }
        try {
            
        } catch (Exception $e) {
            dd($e->getMessage());
        }
    }
}
