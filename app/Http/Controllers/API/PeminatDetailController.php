<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Peminat;
use App\PeminatDetail;
use App\Rules\table_column;
use App\Rules\unique_with;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class PeminatDetailController extends Controller
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
            $peminat_detail = PeminatDetail::all();
            $response = [
                'status' => 200,
                'data' => $peminat_detail
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
        // validasi input
        $rules = [
            'peminat_id' => ['required', 'exists:peminat,id,deleted_at,NULL'],
            'kode_matkul' => ['required', 'exists:mata_kuliah,kode_matkul,deleted_at,NULL', new unique_with('peminat_detail,peminat_id,' . $request->peminat_id . ',kode_matkul,' . $request->kode_matkul . ',deleted_at,NULL')],
            'jumlah_peminat' => ['required', 'numeric']
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
            'kode_matkul' => $request->kode_matkul,
            'jumlah_peminat' => $request->jumlah_peminat,
            'peminat_id' => $request->peminat_id,
            'created_at' => date('Y-m-d H:i:s', time()),
            'updated_at' => date('Y-m-d H:i:s', time()),
        ];


        try {
            PeminatDetail::insert($insertToDB);
            $response = [
                'status' => 200,
                'message' => "Berhasil Menambahkan Peminat Mata Kuliah " . $request->kode_matkul . " dengan Jumlah Peminat " . $request->jumlah_peminat . "."
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $table_column = collect($request->all())->keys()->toArray();
        $rules = [
            'column' => ['required', new table_column('peminat_detail')]
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
            $peminat_detail = PeminatDetail::where($request->all())->get();
            $response = [
                'status' => 200,
                'data' => $peminat_detail
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $peminat_detail = PeminatDetail::find($request->id);
        try {
            $request->request->add(['peminat_id' => $peminat_detail->peminat_id]);
        } catch (Exception $e) {
        }

        // validasi input
        $rules = [
            'id' => ['bail', 'required', 'exists:peminat_detail,id,deleted_at,NULL'],
            'kode_matkul' => ['required', 'exists:mata_kuliah,kode_matkul,deleted_at,NULL', new unique_with('peminat_detail,peminat_id,' . $request->peminat_id . ',kode_matkul,' . $request->kode_matkul . ',deleted_at,NULL', 'id,' . $request->id)],
            'jumlah_peminat' => ['required', 'numeric']
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $response = [
                'status' => 400,
                'message' => $validator->errors()
            ];
            return response()->json($response, $response['status']);
        }


        $peminat_detail = PeminatDetail::find($request->id);
        if ($peminat_detail->kode_matkul == $request->kode_matkul) {
            $request->request->remove('kode_matkul');
        }


        // update key only
        $accepted_key = collect($rules)->except('id')->keys();
        $update = collect($request->all())->only($accepted_key);


        try {
            $update->map(function ($item, $key) use ($peminat_detail) {
                $peminat_detail[$key] = $item;
            });
            $peminat_detail->save();
            $response = [
                'status' => 200,
                'message' => "Berhasil Mengubah Peminat Mata Kuliah " . $peminat_detail->mata_kuliah->nama_matkul . " (" . $peminat_detail->kode_matkul . ") dengan Jumlah Peminat " . $peminat_detail->jumlah_peminat . "."
            ];
            if (!$peminat_detail->getChanges()) {
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $rules = [
            'id' => ['required', 'exists:peminat_detail,id,deleted_at,NULL']
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
            $peminat_detail = PeminatDetail::find($request->id);
            $peminat_detail->delete();
            $response = [
                'status' => 200,
                'message' => 'Peminat Mata Kuliah ' . $peminat_detail->mata_kuliah->nama_matkul . ' (' . $peminat_detail->kode_matkul . ') berhasil dihapus.'
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
