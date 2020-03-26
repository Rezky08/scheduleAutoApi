<?php

namespace App\Http\Controllers\API;

use App\Dosen;
use App\Helpers\Host;
use App\Helpers\Request_api;
use App\Http\Controllers\Controller;
use App\Jadwal;
use App\Jobs\PythonDosen;
use App\Krs;
use App\Matakuliah;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Response;
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
        $job = new PythonDosen();
        $job->dispatch('dosen');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        // Validation
        $rules = [
            // for get kelompok
            'kode_krs' => ['required', 'exists:krs,kode_krs,deleted_at,NULL'],
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

            // for jadwal
            'tahun_ajar' => ['required'],
            'semester' => ['required', 'in:E,O'],
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $response = [
                'status' => 400,
                'message' => $validator->errors()
            ];
            return response()->json($response, 400);
        }
        // prepare for send to python engine
        $whereCond = [
            'kode_krs' => $request->kode_krs
        ];
        $krs_matkul = Krs::where($whereCond)->first()->krs_matkul;
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

        // send request to python engine and get kelompok
        $host = new Host();
        $url = $host->host('python_engine') . 'kelompok';
        $client = new Client();
        try {
            $res = $client->request('POST', $url, ['json' => $form_params]);
            $contents = $res->getBody()->getContents();
            $contents = json_decode($contents);
            $matkul_kelompok = collect($contents);
            $matkul_kelompok = $matkul_kelompok->map(function ($item, $index) {
                $item = collect($item)->toArray();
                return $item;
            });
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


        // prepare to get dosen algen
        $matkul = Matakuliah::all();
        $dosen_by_matkul = $matkul->mapWithKeys(function ($item, $index) {
            $dosen = $item->dosen_matkul->map(function ($item, $index) {
                return $item->kode_dosen;
            });
            $dosen = $dosen->toArray();
            return [$item->kode_matkul => $dosen];
        });
        $dosen_by_matkul = $dosen_by_matkul->map(function ($item, $key) {
            $item_new = [
                'kode_matkul' => $key,
                'kode_dosen' => $item
            ];
            return $item_new;
        })->values();
        $dosen_by_matkul = [
            'kode_matkul' => $dosen_by_matkul->pluck('kode_matkul'),
            'kode_dosen' => $dosen_by_matkul->pluck('kode_dosen')
        ];

        $rules = [
            'max_kelompok' => $request->max_kelompok
        ];


        $form_params = [
            'nn_params' => [
                'mata_kuliah' => $matkul_kelompok,
                'matkul_dosen' => $dosen_by_matkul
            ],
            'rules' => $rules,
            'num_generation' => $request->num_generation,
            'num_population' => $request->num_population,
            'crossover_rate' => $request->crossover_rate / 100,
            'mutation_rate' => $request->mutation_rate / 100,
            'timeout' => $request->timeout
        ];

        // send request to python engine and get kelompok
        $host = new Host();
        $url = $host->host('python_engine') . 'dosen';
        $client = new Client();
        $res = $client->requestAsync('POST', $url, ['json' => $form_params])->then(
            function ($result) {
                $contents = $result->getBody()->getContents();
                $contents = json_decode($contents);
                $response = [
                    'status' => $result->getStatusCode(),
                    'data' => $contents
                ];
                return $response;
            },
            function ($error) {
                $contents = $error->getResponse()->getBody()->getContents();
                $message = $error->getResponse()->getMessage();
                $response = [
                    'status' => $error->getStatusCode(),
                    'message' => $message
                ];
                return $response;
                // return response()->json($response, $error->getResponse()->getStatusCode());
            }
        );

        // insert porcess start
        $kode_jadwal_pre = date('Ymd', time());
        $kode_jadwal = Jadwal::where('kode_jadwal', 'like', $kode_jadwal_pre . '%')->orderBy('kode_jadwal', 'desc')->first();
        if (!is_null($kode_jadwal)) {
            $kode_jadwal = substr($kode_jadwal->kode_jadwal, strlen($kode_jadwal_pre));
            $kode_jadwal = (int) $kode_jadwal + 1;
            $kode_jadwal = $kode_jadwal_pre . $kode_jadwal;
        } else {
            $kode_jadwal = $kode_jadwal_pre . '0';
        }
        try {
            // Insert To Jadwal
            $dataInsert = [
                'kode_jadwal' => $kode_jadwal,
                'tahun_ajar' => $request->tahun_ajar,
                'semester' => $request->semester,
                'created_at' => date('Y-m-d H:i:s', time()),
                'updated_at' => date('Y-m-d H:i:s', time()),
            ];
            $insertId = Jadwal::insertGetId($dataInsert);
        } catch (\Throwable $th) {
            //throw $th;
        }


        $res = $res->wait();
        if ($res['status'] == 200) {
            try {
                // update status jadwal
                $dataUpdate = [
                    'status_jadwal' => true
                ];
                $whereCond = [
                    'kode_jadwal' => $kode_jadwal
                ];
                $result = Jadwal::where($whereCond)->update($dataUpdate);
            } catch (\Throwable $th) {
                $whereCond = [
                    'id' => $insertId
                ];
                $delete = Jadwal::delete();
                return response()->json($res, $res['status']);
            }
        }

        return response()->json($res, 200);
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
