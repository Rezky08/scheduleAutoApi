<?php

namespace App\Listener;

use App\Event\AlgenKelompokDosenProcess;
use App\Http\Controllers\API\AlgenResultLogController;
use App\Http\Controllers\API\KelompokDosenController;
use App\Http\Controllers\API\KelompokDosenDetailController;
use App\KelompokDosen;
use App\ProcessLogDetail;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Http\Request;
use Illuminate\Queue\InteractsWithQueue;

class AlgenKelompokDosenListener implements ShouldQueue
{

    public $tries = 5;
    public $retryAfter = 5;
    public $timeout = 0;
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
     * @param  AlgenKelompokDosenProcess  $event
     * @return void
     */
    public function handle(AlgenKelompokDosenProcess $event)
    {
        // prepare for get dosen combination
        $dosen_matkul = $event->peminat->peminat_detail->mapWithKeys(function ($item) {
            $dosen_matkul = $item->mata_kuliah->dosen_matkul;
            return [$item->kode_matkul => $dosen_matkul->pluck('kode_dosen')->toArray()];
        });
        $dosen_matkul = [
            'kode_matkul' => $dosen_matkul->keys()->toArray(),
            'kode_dosen' => $dosen_matkul->values()->toArray()
        ];

        $form_params = [
            'nn_params' => [
                'mata_kuliah' => $event->kelompok_matkul,
                'matkul_dosen' => $dosen_matkul
            ]
        ];
        $params = $event->config;
        $form_params += $params;

        $client = new Client();
        $reqAsync = $client->requestAsync('POST', 'http://localhost:5000/dosen', ['json' => $form_params]);


        // add log detail

        $insertToDB = [
            'process_log_id' => $event->process->id,
            'description' => "Mulai Algen Process Mata Kuliah Kelompok Dosen",
            'created_at' => new \DateTime
        ];
        ProcessLogDetail::insert($insertToDB);

        // update process status to success
        $event->process->status = 1;
        $event->process->save();

        $reqAsync->then(function ($response) use ($event) {
            $insertToDB = [
                'process_log_id' => $event->process->id,
                'description' => "Berhasil Algen Process Mata Kuliah Kelompok Dosen",
                'created_at' => new \DateTime
            ];
            ProcessLogDetail::insert($insertToDB);

            return $response;
        }, function ($response) use ($event) {
            $insertToDB = [
                'process_log_id' => $event->process->id,
                'description' => "Gagal Algen Process Mata Kuliah Kelompok Dosen Exception: " . $response->getMessage(),
                'created_at' => new \DateTime
            ];
            ProcessLogDetail::insert($insertToDB);

            return $response;
        });

        $response = $reqAsync->wait();
        $result = $response->getBody()->getContents();
        // dapat kelompok dosen
        $kelompok_dosen = json_decode($result);
        $kelompok_dosen = $kelompok_dosen->results;

        foreach ($kelompok_dosen as $key => $item) {

            // insert kelompok dosen
            try {
                $insertToDB = [
                    'peminat_id' => $event->peminat->id,
                ];
                $request = new Request();
                $request->setMethod('POST');
                $request->request->add($insertToDB);
                $kelompokDosenController = new KelompokDosenController();
                $result = $kelompokDosenController->store($request);
                if ($result->getStatusCode() != 200) {
                    return $result;
                }

                $kelompok_dosen_model = KelompokDosen::orderBy('id', 'desc')->first();

                // insert algen result
                $insertToDB = [
                    'process_log_id' => $event->process->id,
                    'result_key' => $kelompok_dosen_model->id,
                    'fit_score' => $item->fit_score * 100
                ];
                $request = new Request();
                $request->setMethod('POST');
                $request->request->add($insertToDB);

                $algenResult = new AlgenResultLogController();
                $result = $algenResult->store($request);
                if ($result->getStatusCode() != 200) {
                    return $result;
                }
            } catch (Exception $e) {
                $insertToDB = [
                    'process_log_id' => $event->process->id,
                    'description' => "Gagal Menambahkan Mata Kuliah Kelompok Dosen" . $e->getMessage(),
                    'created_at' => new \DateTime
                ];
                ProcessLogDetail::insert($insertToDB);
            }

            // insert kelompok dosen detail
            foreach ($item->data as $key => $kelompok_dosen_detail) {
                try {
                    $insertToDB = collect($kelompok_dosen_detail)->toArray();
                    $insertToDB['kelompok_dosen_id'] = $kelompok_dosen_model->id;
                    $request = new Request();
                    $request->setMethod('POST');
                    $request->request->add($insertToDB);
                    $kelompokDosenDetailController = new KelompokDosenDetailController();
                    $result = $kelompokDosenDetailController->store($request);
                    if ($result->getStatusCode() != 200) {
                        return $result;
                    }
                } catch (Exception $e) {
                    $insertToDB = [
                        'process_log_id' => $event->process->id,
                        'description' => "Gagal Menambahkan Mata Kuliah Kelompok Dosen" . $e->getMessage(),
                        'created_at' => new \DateTime
                    ];
                    ProcessLogDetail::insert($insertToDB);
                }
            }
        }
    }
}
