<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\KelompokDosen;
use App\Rules\table_column;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class KelompokDosenController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (count($request->all()) > 0) {
            return $this->show($request);
        }
        try {
            $kelompok_dosen = KelompokDosen::all();
            $response = [
                'status' => 200,
                'data' => $kelompok_dosen
            ];
            return response()->json($response, $response['status']);
        } catch (Exception $e) {
            $response = [
                'status' => 500,
                'message' => "Internal Server Error"
            ];
            if (env('APP_DEBUG') == true) {
                $response['message'] = $e->getMessage();
            }
            return response()->json($response, $response['status']);
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
        $rules = [
            'peminat_id' => ['required', 'exists:peminat,id,deleted_at,NULL']
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $response = [
                'status' => 400,
                'message' => $validator->errors()
            ];
            return response()->json($response, $response['status']);
        }

        $insertToDB = [
            'peminat_id' => $request->peminat_id,
            'created_at' => date('Y-m-d H:i:s', time()),
            'updated_at' => date('Y-m-d H:i:s', time()),
        ];

        try {
            KelompokDosen::insert($insertToDB);
            $response = [
                'status' => 200,
                'message' => "Mata Kuliah Kelompok Dosen Berhasil Ditambahkan."
            ];
            return response()->json($response, $response['status']);
        } catch (Exception $e) {
            $response = [
                'status' => 500,
                'message' => "Internal Server Error"
            ];
            if (env('APP_DEBUG') == true) {
                $response['message'] = $e->getMessage();
            }
            return response()->json($response, $response['status']);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\KelompokDosen  $kelompokDosen
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $table_column = collect($request->all())->keys()->toArray();
        $rules = [
            'column' => ['required', new table_column('kelompok_dosen')]
        ];
        $validator = Validator::make($request->all() + ['column' => $table_column], $rules);
        if ($validator->fails()) {
            $response = [
                'status' => 400,
                'message' => $validator->errors()
            ];
            return response()->json($response, $response['status']);
        }


        try {
            $kelompok_dosen = KelompokDosen::where($request->all())->get();
            $response = [
                'status' => 200,
                'data' => $kelompok_dosen
            ];
            return response()->json($response, $response['status']);
        } catch (Exception $e) {
            $response = [
                'status' => 500,
                'message' => "Internal Server Error"
            ];
            if (env('APP_DEBUG') == true) {
                $response['message'] = $e->getMessage();
            }
            return response()->json($response, $response['status']);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\KelompokDosen  $kelompokDosen
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $rules = [
            'id' => ['required', 'exists:kelompok_dosen,id,deleted_at,NULL'],
            'peminat_id' => ['required', 'exists:peminat,id,deleted_at,NULL']
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $response = [
                'status' => 400,
                'message' => $validator->errors()
            ];
            return response()->json($response, $response['status']);
        }

        // update key only
        $accepted_key = collect($rules)->except('id')->keys();
        $update = collect($request->all())->only($accepted_key);

        try {
            $kelompok_dosen = KelompokDosen::find($request->id);
            $update->map(function ($item, $key) use ($kelompok_dosen) {
                $kelompok_dosen[$key] = $item;
            });
            $kelompok_dosen->save();
            $response = [
                'status' => 200,
                'message' => "Mata Kuliah Kelompok Dosen dengan Kode " . $kelompok_dosen->id . " Berhasil diubah."
            ];
            if (!$kelompok_dosen->getChanges()) {
                $response['message'] = "Tidak ada perubahan";
            }
            return response()->json($response, $response['status']);
        } catch (Exception $e) {
            $response = [
                'status' => 500,
                'message' => "Internal Server Error"
            ];
            if (env('APP_DEBUG') == true) {
                $response['message'] = $e->getMessage();
            }
            return response()->json($response, $response['status']);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\KelompokDosen  $kelompokDosen
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {

        $rules = [
            'id' => ['required', 'exists:kelompok_dosen,id,deleted_at,NULL']
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $response = [
                'status' => 400,
                'message' => $validator->errors()
            ];
            return response()->json($response, $response['status']);
        }

        try {
            $kelompok_dosen = KelompokDosen::find($request->id);
            $kelompok_dosen->delete();
            $response = [
                'status' => 200,
                'message' => 'Mata Kuliah Kelompok Dosen dengan Kode ' . $kelompok_dosen->id . ' berhasil dihapus.'
            ];
            return response()->json($response, $response['status']);
        } catch (Exception $e) {
            $response = [
                'status' => 500,
                'message' => "Internal Server Error"
            ];
            if (env('APP_DEBUG') == true) {
                $response['message'] = $e->getMessage();
            }
            return response()->json($response, $response['status']);
        }
    }
}
