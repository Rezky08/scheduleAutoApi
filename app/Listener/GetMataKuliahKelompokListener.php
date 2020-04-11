<?php

namespace App\Listener;

use App\Event\GetMataKuliahKelompok;
use App\ProcessLogDetail;
use GuzzleHttp\Client;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Helpers\Host;

class GetMataKuliahKelompokListener
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
     * @param  GetMataKuliahKelompok  $event
     * @return void
     */
    public function handle(GetMataKuliahKelompok $event)
    {

        // persiapan untuk pembagian kelompok mata kuliah
        $peminat_params = $event->peminat->peminat_detail->map(function ($item) {
            $item = [
                'kode_matkul' => $item->kode_matkul,
                'jumlah_peminat' => $item->jumlah_peminat,
                'lab_matkul' => $item->mata_kuliah->lab_matkul,
            ];
            return $item;
        });
        $peminat_props = $event->config;
        $form_params = [
            'peminat_params' => $peminat_params->toArray(),
            'peminat_props' => $peminat_props
        ];

        // send to flask untuk membagi kelompok
        $client = new Client();
        $host = new Host();
        $url = $host->host('python_engine').'kelompok';
        $reqAsync = $client->requestAsync('POST',$url , ['json' => $form_params]+$event->headers);

        // add log detail
        $insertToDB = [
            'process_log_id' => $event->process->id,
            'description' => "Mulai Bagi Kelompok Mata Kuliah",
            'created_at' => new \DateTime
        ];
        ProcessLogDetail::insert($insertToDB);

        $reqAsync->then(function ($response) use ($event) {
            $insertToDB = [
                'process_log_id' => $event->process->id,
                'description' => "Berhasil Bagi Kelompok Mata Kuliah",
                'created_at' => new \DateTime
            ];
            ProcessLogDetail::insert($insertToDB);
            return $response;
        }, function ($response) use ($event) {
            $insertToDB = [
                'process_log_id' => $event->process->id,
                'description' => "Gagal Bagi Kelompok Mata Kuliah Exception: " . $response->getMessage(),
                'created_at' => new \DateTime
            ];
            ProcessLogDetail::insert($insertToDB);

            // add process attempt
            $event->process->attempt += 1;
            $event->process->save();

            return $response;
        });
        $response = $reqAsync->wait();
        if ($response->getStatusCode() != 200) {
            return $response;
        }
        $result = $response->getBody()->getContents();
        // dapat kelompok mata kuliah
        $kelompok_matkul = json_decode($result);
        return $kelompok_matkul;
    }
}
