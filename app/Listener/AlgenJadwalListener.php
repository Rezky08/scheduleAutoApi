<?php

namespace App\Listener;

use App\Event\AlgenJadwalProcess;
use App\Event\StoreResultJadwal;
use App\Helpers\Host;
use GuzzleHttp\Client;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Http\Request;
use Illuminate\Queue\InteractsWithQueue;

class AlgenJadwalListener implements ShouldQueue
{
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
     * @param  AlgenJadwalProcess  $event
     * @return void
     */
    public function handle(AlgenJadwalProcess $event)
    {
        $form_params = $event->params + $event->config;
        $client = new Client();
        $host = new Host();
        $url = $host->host('python_engine') . 'jadwal';
        // get celery_id
        // $res = $client->requestAsync('POST', $url, ['json' => $form_params] + $event->headers);
        // $res = $res->wait();
        // if ($res->getStatusCode() != 200) {
        //     $event->process->attempt += 1;
        //     $event->process->save();
        //     echo "Gagal Send Process Jadwal";
        //     return false;
        // }
        // $res = $res->getBody()->getContents();
        // $res = json_decode($res);
        // $celery_id = $res->celery_id;


        // add log detail
        $insertToDB = [
            'process_log_id' => $event->process->id,
            'description' => "Mulai Algen Process Jadwal",
            // 'created_at' => new \DateTime
        ];
        $request = new Request();
        $request->setMethod("POST");
        $request->request->add($insertToDB);
        $response = $event->process_log_detail_controller->store($request);
        if ($response->getStatusCode() != 200) {
            echo "Failed Write Log Detail";
            return $response;
        }

        // Get Result
        while (true) {
            $form_params = [
                // 'celery_id' => $celery_id
                'celery_id' => '1d266752-31ca-4bc1-b34e-97f336c59c97'
            ];
            
            $url = $host->host('python_engine') . 'result';
            $res = $client->requestAsync('GET', $url, ['json' => $form_params] + $event->headers);
            $res = $res->wait();
            

            if ($res->getStatusCode() != 200) {
                $event->process->attempt += 1;
                $event->process->save();
                echo "Gagal Get Result";
                return false;
            }

            $res = $res->getBody()->getContents();
            $res = json_decode($res);
            if ($res->status == "SUCCESS") {
                $jadwal_result = $res->result;

                $insertToDB = [
                    'process_log_id' => $event->process->id,
                    'description' => "Berhasil Algen Process Jadwal",
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
                    'description' => "Gagal Algen Process Jadwal",
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
            echo "\n" . $res->status;
            // retry delay
            sleep(10);
        }

        // update process attempt
        $event->process->status = 1;
        $event->process->save();
        echo ("Mulai insert jadwal");
        // dd($jadwal_result);
        event(new StoreResultJadwal($event->process, $jadwal_result));
    }
}
