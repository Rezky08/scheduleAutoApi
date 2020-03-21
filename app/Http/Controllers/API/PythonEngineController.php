<?php

namespace App\Http\Controllers\API;

use App\Helpers\Host;
use App\Helpers\Request_api;
use App\Http\Controllers\Controller;
use App\Krs;
use App\Matakuliah;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Response;
use Illuminate\Http\Request;

class PythonEngineController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $krs_matkul = Krs::first()->krs_matkul;
        $peminat_params = $krs_matkul->map(function ($item, $index) {
            $item = [
                'kode_matkul' => $item->kode_matkul,
                'jumlah_krs' => (int) $item->jumlah_krs,
                'lab' => $item->matkul->lab_matkul
            ];
            return $item;
        });
        $peminat_params = $peminat_params->toArray();
        $peminat_props = [
            'min_perkelas'  => 20,
            'max_perkelas'  => 50,
            'min_perlab'    => 15,
            'max_perlab'    => 50
        ];
        $form_params = [
            'peminat_params'    => $peminat_params,
            'peminat_props'     => $peminat_props
        ];

        $host = new Host();
        $url = $host->host('python_engine') . 'bagi_kelompok';
        $client = new Client();
        try {
            $res = $client->request('POST', $url, ['json' => $form_params]);
            $contents = $res->getBody()->getContents();
            $contents = json_decode($contents);
            $response = [
                'status' => $res->getStatusCode(),
                'data' => $contents
            ];
            return response()->json($response, 200);
        } catch (GuzzleException $e) {
            $contents = $e->getResponse()->getBody()->getContents();
            $status_code = $e->getResponse()->getStatusCode();
            $contents = json_decode($contents);
            $response = [
                'status' => $status_code,
                'message' => $contents
            ];
            return response()->json($response, $status_code);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
