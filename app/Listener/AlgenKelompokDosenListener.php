<?php

namespace App\Listener;

use App\Event\AlgenKelompokDosenProcess;
use App\Http\Controllers\API\AlgenResultLogController;
use App\Http\Controllers\API\KelompokDosenController;
use App\Http\Controllers\API\KelompokDosenDetailController;
use App\Helpers\Host;
use App\KelompokDosen;
use App\ProcessLogDetail;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Http\Request;
use Illuminate\Queue\InteractsWithQueue;

class AlgenKelompokDosenListener
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
        $form_params['process_log_id'] = $event->process->id;

        $client = new Client();
        $host = new Host();
        $url = $host->host('python_engine') . 'dosen';
        // get celery_id
        $res = $client->request('POST', $url, ['json' => $form_params] + $event->headers);
        if ($res->getStatusCode() != 200) {
            $event->process->attempt += 1;
            $event->process->save();
            return false;
        }
        $res = $res->getBody()->getContents();
        $res = json_decode($res);
        $celery_id = $res->celery_id;


        // add log detail

        $insertToDB = [
            'process_log_id' => $event->process->id,
            'description' => "Mulai Algen Process Mata Kuliah Kelompok Dosen",
            'created_at' => new \DateTime
        ];
        ProcessLogDetail::insert($insertToDB);

        // Get Result
        $form_params = [
            'celery_id' => $celery_id
        ];
        $url = $host->host('python_engine') . 'dosen/result';
        $res = $client->request('GET', $url, ['json' => $form_params] + $event->headers);

        if ($res->getStatusCode() != 200) {
            $event->process->attempt += 1;
            $event->process->save();
            return false;
        }

        while (true) {
            $res = $res->getBody()->getContents();
            $res = json_decode($res);
            if ($res->status == "SUCCESS") {
                return dd($res);
            }
        }


        // update process attempt
        $event->process->attempt += 1;
        $event->process->save();

        $reqAsync->then(function ($response) use ($event) {
            $insertToDB = [
                'process_log_id' => $event->process->id,
                'description' => "Berhasil Algen Process Mata Kuliah Kelompok Dosen",
                'created_at' => new \DateTime
            ];
            ProcessLogDetail::insert($insertToDB);

            // update process attempt
            $event->process->status = 1;
            $event->process->save();

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
    }
}
