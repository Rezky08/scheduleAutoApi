<?php

namespace App\Listener;

use App\Event\AlgenJadwalProcess;
use App\Helpers\Host;
use GuzzleHttp\Client;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class AlgenJadwalListener
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
     * @param  AlgenJadwalProcess  $event
     * @return void
     */
    public function handle(AlgenJadwalProcess $event)
    {
        $form_params = $event->params + $event->config;
        dd($form_params);
        $client = new Client();
        $host = new Host();
        $url = $host->host('python_engine') . 'dosen';
        // get celery_id
        $res = $client->request('POST', $url, ['json' => $form_params] + $event->headers);
        if ($res->getStatusCode() != 200) {
            $event->process->attempt += 1;
            $event->process->save();
            echo "Gagal Send Process Dosen";
            return false;
        }
        $res = $res->getBody()->getContents();
        $res = json_decode($res);
        $celery_id = $res->celery_id;


        // add log detail
        $insertToDB = [
            'process_log_id' => $event->process->id,
            'description' => "Mulai Algen Process Mata Kuliah Kelompok Dosen",
            // 'created_at' => new \DateTime
        ];
        $request = new Request();
        $request->setMethod("POST");
        $request->request->add($insertToDB);
        $response = $event->process_log_detail_controller->store($request);
        if ($response->getStatusCode() != 200) {
            dd($response);
            echo "Failed Write Log Detail";
            return $response;
        }

        // Get Result
        while (true) {
            $form_params = [
                'celery_id' => $celery_id
            ];
            $url = $host->host('python_engine') . 'dosen/result';
            $res = $client->request('GET', $url, ['json' => $form_params] + $event->headers);

            if ($res->getStatusCode() != 200) {
                $event->process->attempt += 1;
                $event->process->save();
                echo "Gagal Get Result";
                return false;
            }

            $res = $res->getBody()->getContents();
            $res = json_decode($res);
            if ($res->status == "SUCCESS") {
                $kelompok_dosen_result = $res->result;

                $insertToDB = [
                    'process_log_id' => $event->process->id,
                    'description' => "Berhasil Algen Process Mata Kuliah Kelompok Dosen",
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
                echo "succcess";
                break;
            } elseif ($res->status != "PENDING") {
                $insertToDB = [
                    'process_log_id' => $event->process->id,
                    'description' => "Gagal Algen Process Mata Kuliah Kelompok Dosen",
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
                echo "Failure";
                return false;
            }

            // retry delay
            sleep(10);
        }

        // update process attempt
        $event->process->status = 1;
        $event->process->save();
        echo ("Mulai insert kelompok");
        event(new StoreResultKelompokDosen($event->process, $kelompok_dosen_result));
    }
}
