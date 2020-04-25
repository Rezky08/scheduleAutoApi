<?php

namespace App\Listener;

use App\Event\StoreResultJadwal;
use App\Http\Controllers\API\JadwalController;
use App\Jadwal;
use App\KelompokDosen;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Http\Request;
use Illuminate\Queue\InteractsWithQueue;

class StoreResultJadwalListener implements ShouldQueue
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
        $insertToDB = [
            'process_log_id' => $event->process->id,
            'description' => "Mulai menambahkan Jadwal",
            // 'created_at' => new \DateTime
        ];
        // ProcessLogDetail::insert($insertToDB);
        $request = new Request();
        $request->setMethod("POST");
        $request->request->add($insertToDB);
        $response = $event->process_log_detail_controller->store($request);
        if ($response->getStatusCode() != 200) {
            return $response;
        }
    
        $jadwal_controller = new JadwalController();
        $kelompok_dosen = new KelompokDosen();
        $kelompok_dosen = $kelompok_dosen::find($event->process->item_key);
        $peminat = $kelompok_dosen->peminat;
        // store algen result prop
        $insertToDB = [
            'tahun_ajar' => $peminat->tahun_ajaran,
            'semester' => $peminat->semester,
            'created_at' => new \DateTime
        ];
        $jadwal_id = Jadwal::insertGetId($insertToDB);


        $insertToDB = [
            'process_log_id' => $event->process->id,
            'result_key' => $jadwal_id,
            'fit_score' => $event->JadwalResult->fit_score
        ];
        $request = new Request();
        $request->setMethod("POST");
        $request->request->add($insertToDB);
        $response = $event->algen_result_controller->store($request);
        if ($response->getStatusCode() != 200) {
            return $response;
        }

        foreach ($event->JadwalResult->data as $data_key => $data_item) {
            $data_item = collect($data_item)->toArray();
            $data_item['kelompok_dosen_id'] = $jadwal_id;
            $request = new Request();
            $request->setMethod("POST");
            $request->request->add($data_item);
            $response = $jadwal_controller->store($request);
            if ($response->getStatusCode() != 200) {
                return $response;
            }
        }

        $insertToDB = [
            'process_log_id' => $event->process->id,
            'description' => "Berhasil menambahkan Jadwal Detail",
            // 'created_at' => new \DateTime
        ];
        // ProcessLogDetail::insert($insertToDB);
        $request = new Request();
        $request->setMethod("POST");
        $request->request->add($insertToDB);
        $response = $event->process_log_detail_controller->store($request);
        if ($response->getStatusCode() != 200) {
            return $response;
        }
    }
}
