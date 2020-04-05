<?php

namespace App\Http\Controllers\API;

use App\Event\AlgenKelompokDosenProcess;
use App\Event\AlgenProcess;
use App\Event\GetMataKuliahKelompok;
use App\Hari;
use App\Helpers\Host;
use App\Http\Controllers\Controller;
use App\Jadwal;
use App\Jobs\PythonDosen;
use App\Krs;
use App\Matakuliah;
use App\Peminat;
use App\ProcessLog;
use App\Ruang;
use App\Sesi;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PythonEngineController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
    }

    public function storeJadwal(Request $request)
    {
        $request_casts = [];
        foreach ($request->all() as $key => $value) {
            $request_casts[$key] = (int) $value;
        }
        $ruang = Ruang::all();
        $hari = Hari::all();
        $sesi = Sesi::all();
        // combine [id_ruang,id_hari,id_sesi]
        $combine = [];
        $ruang->map(function ($ruang_item) use ($hari, $sesi, &$combine) {
            $hari->map(function ($hari_item) use ($sesi, $ruang_item, &$combine) {
                $sesi->map(function ($sesi_item) use ($ruang_item, $hari_item, &$combine) {
                    $combine[] = [
                        $ruang_item->id,
                        $hari_item->id,
                        $sesi_item->id
                    ];
                });
            });
        });
        dd($ruang->count(), $hari->count(), $sesi->count(), $combine);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeKelompokDosen(Request $request)
    {


        // Validation
        $rules = [
            // for get kelompok
            'peminat_id' => ['required', 'exists:peminat,id,deleted_at,NULL'],
            'min_perkelas' => ['required', 'numeric'],
            'max_perkelas' => ['required', 'numeric'],
            'min_perlab' => ['required', 'numeric'],
            'max_perlab' => ['required', 'numeric'],

            // for get dosen
            'max_kelompok' => ['required', 'numeric'],
            'crossover_rate' => ['required', 'between:0,100'],
            'mutation_rate' => ['required', 'between:0,100'],
            'num_generation' => ['required', 'numeric'],
            'num_population' => ['required', 'numeric'],
            'timeout' => ['required', 'sometimes'],
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $response = [
                'status' => 400,
                'message' => $validator->errors()
            ];
            return response()->json($response, $response['status']);
        }

        $peminat_props = [
            'min_perkelas'  => $request->min_perkelas,
            'max_perkelas'  => $request->max_perkelas,
            'min_perlab'    => $request->min_perlab,
            'max_perlab'    => $request->max_perlab
        ];

        // insert process log
        $peminat = Peminat::find($request->peminat_id);

        $insertToDB = [
            'process_item_id' => 1,
            'item_key' => $request->peminat_id,
            'status' => 0,
            'attempt' => 0,
            'created_at' => new \Datetime
        ];
        $process_log_id = ProcessLog::insertGetId($insertToDB);
        $process = ProcessLog::find($process_log_id);

        try {
            // get kelompok matkul
            $kelompok_matkul = event(new GetMataKuliahKelompok($process, $peminat, $peminat_props));
            $kelompok_matkul = $kelompok_matkul[0];
        } catch (Exception $e) {
            $response = [
                'status' => 500,
                'message' => 'Internal Server Error'
            ];
            if (env('APP_DEBUG') == true) {
                $response['message'] = $e->getMessage();
            }
            return response()->json($response, $response['status']);
        }

        // get dosen
        $rules = [
            'max_kelompok' => $request->max_kelompok
        ];

        $config = [
            'rules' => $rules,
            'num_generation' => $request->num_generation,
            'num_population' => $request->num_population,
            'crossover_rate' => $request->crossover_rate / 100,
            'mutation_rate' => $request->mutation_rate / 100,
            'timeout' => $request->timeout
        ];
        event(new AlgenKelompokDosenProcess($process, $peminat, $config, $kelompok_matkul));

        $response = [
            'status' => 200,
            'message' => "Process Already Running"
        ];
        return response()->json($response, $response['status']);
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
