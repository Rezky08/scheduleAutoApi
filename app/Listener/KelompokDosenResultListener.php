<?php

namespace App\Listener;

use App\Event\CatchKelompokDosenResult;
use App\Http\Controllers\API\KelompokDosenController;
use App\Http\Controllers\API\KelompokDosenDetailController;
use App\Http\Controllers\API\ProcessLogController;
use App\Http\Controllers\API\ProcessLogDetailController;
use App\KelompokDosen;
use App\Peminat;
use App\ProcessLog;
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
        $process_log_detail_controller = new ProcessLogDetailController();
        $process_log_controller = new ProcessLogController();
        // check result status

        $kelompok_dosen_results = json_decode($event->request['results']);
        $process = ProcessLog::find($event->request['process_log_id']);
        $kelompok_dosen_detail_controller = new KelompokDosenDetailController();

        if ($event->request['status'] != 200) {
            $insertToProcessLogDetail = [
                'process_log_id' => $process->id,
                'description' => 'Gagal Algen Kelompok Dosen ' . $event->request['error']
            ];
            $request = new Request();
            $request->setMethod('POST');
            $request->request->add($insertToProcessLogDetail);
            $response = $process_log_detail_controller->store($request);
            return $response;
        }

        foreach ($kelompok_dosen_results as $key => $kelompok_dosen) {
            $kelompok_dosen = collect($kelompok_dosen)->toArray();

            $insertToKelompokDosen = [
                'peminat_id' => $process->item_key,
                'created_at' => new \DateTime
            ];

            try {
                $kelompok_dosen_id = KelompokDosen::insertGetId($insertToKelompokDosen);
            } catch (Exception $e) {
                // INSERT LOG DETAIL
                $insertToProcessLogDetail = [
                    'process_log_id' => $process->id,
                    'description' => 'Kelompok Dosen ' . $e->getMessage()
                ];
                $request = new Request();
                $request->setMethod('POST');
                $request->request->add($insertToProcessLogDetail);
                $process_log_detail_controller->store($request);
                return false;
            }

            // insert kelompok dosen detail
            foreach ($kelompok_dosen['data'] as $key => $item) {
                $insertToKelompokDosenDetail = [
                    'kelompok_dosen_id' => $kelompok_dosen_id
                ];
                $item = collect($item)->toArray();

                $insertToKelompokDosenDetail += $item;
                $request = new Request();
                $request->setMethod('POST');
                $request->request->add($insertToKelompokDosenDetail);
                $response = $kelompok_dosen_detail_controller->store($request);
                if ($response->getStatusCode() != 200) {
                    return $response;
                }
            }
        }

        $updateProcessLog = [
            'id' => $process->id,
            'status' => 1
        ];
        $request = new Request();
        $request->setMethod('POST');
        $request->request->add($updateProcessLog);
        $response = $process_log_controller->update($request);
        return $response;
    }
}
