<?php

namespace App\Listener;

use App\Event\StoreResultKelompokDosen;
use App\Http\Controllers\API\KelompokDosenController;
use App\Http\Controllers\API\KelompokDosenDetailController;
use App\KelompokDosen;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Http\Request;
use Illuminate\Queue\InteractsWithQueue;

class StoreResultKelompokDosenListener implements ShouldQueue
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
        $insertToDB = [
            'process_log_id' => $event->process->id,
            'description' => "Mulai menambahkan Mata Kuliah Kelompok Dosen",
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

        $kelompok_dosen_detail_controller = new KelompokDosenDetailController();
        // store algen result prop
        foreach ($event->KelompokDosenResults as $result_key => $result_item) {
            $insertToDB = [
                'peminat_id' => $event->process->item_key,
                'created_at' => new \DateTime
            ];
            $kelompok_dosen_id = KelompokDosen::insertGetId($insertToDB);

            $insertToDB = [
                'process_log_id' => $event->process->id,
                'result_key' => $kelompok_dosen_id,
                'fit_score' => $result_item->fit_score
            ];
            $request = new Request();
            $request->setMethod("POST");
            $request->request->add($insertToDB);
            $response = $event->algen_result_controller->store($request);
            if ($response->getStatusCode() != 200) {
                return $response;
            }

            foreach ($result_item->data as $data_key => $data_item) {
                $data_item = collect($data_item)->toArray();
                $data_item['kelompok_dosen_id'] = $kelompok_dosen_id;
                $request = new Request();
                $request->setMethod("POST");
                $request->request->add($data_item);
                $response = $kelompok_dosen_detail_controller->store($request);
                if ($response->getStatusCode() != 200) {
                    return $response;
                }
            }
        }

        $insertToDB = [
            'process_log_id' => $event->process->id,
            'description' => "Berhasil menambahkan Mata Kuliah Kelompok Dosen",
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
