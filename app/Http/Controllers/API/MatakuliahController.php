<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Matakuliah as mata_kuliah;
use App\Rules\table_column;
use App\Rules\unique_with;
use Exception;

class MatakuliahController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    // menampilkan semua yang ada dalam table mata kuliah
    public function index(Request $request)
    {
        // check apakah ada request kode_matkul
        if (count($request->all()) > 0) {
            return $this->show($request);
        }

        try {
            $mata_kuliah = mata_kuliah::all();
            $response = [
                'status' => 200,
                'data' => $mata_kuliah
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
    // menambahkan mata kuliah ke database
    public function store(Request $request)
    {
        $rules = [
            'kode_matkul' => ['required', 'unique:mata_kuliah,kode_matkul,NULL,deleted_at,NULL', 'max:10'],
            'sks_matkul' => ['required', 'numeric'],
            'nama_matkul' => ['required'],
            'status_matkul' => ['boolean'],
            'kode_prodi' => ['required', 'exists:program_studi,kode_prodi,deleted_at,NULL'],
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
            'sks_matkul' => $request->sks_matkul,
            'nama_matkul' => $request->nama_matkul,
            'status_matkul' => $request->status_matkul,
            'kode_prodi' => $request->kode_prodi,
            'created_at' => now(),
            'updated_at' => now()
        ];
        try {
            mata_kuliah::insert($insertToDB);
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

        $response = [
            'status' => 200,
            'message' => 'Mata Kuliah ' . $request->nama_matkul . ' (' . $request->kode_matkul . ') Berhasil ditambahkan'
        ];
        return response()->json($response, $response['status']);
    }

    /**
     * Display the specified resource.
     *
     * @param  string
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $table_column = collect($request->all())->keys()->toArray();
        $rules = [
            'column' => ['required', new table_column('mata_kuliah')]
        ];
        $validator = Validator::make($request->all() + ['column' => $table_column], $rules);
        if ($validator->fails()) {
            $response = [
                'status' => 400,
                'message' => $validator->errors()
            ];
            return response()->json($response, $response['status']);
        }

        $whereCond = collect($request->all());
        $whereCond = $whereCond->map(function ($item) {
            return $item;
        });

        try {
            $mata_kuliah = mata_kuliah::where($request->all())->get();
            $response = [
                'status' => 200,
                'data' => $mata_kuliah
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

        $rules = [
            'id' => ['required', 'exists:mata_kuliah,id,deleted_at,NULL'],
            'kode_matkul' => ['required', 'exists:mata_kuliah,kode_matkul,deleted_at,NULL', new unique_with('mata_kuliah,kode_matkul,' . $request->kode_matkul . ',deleted_at,NULL', 'id,' . $request->id)],
            'sks_matkul' => ['required', 'numeric'],
            'nama_matkul' => ['required', 'max:100'],
            'status_matkul' => ['required', 'boolean'],
            'lab_matkul' => ['required', 'boolean'],
            'kode_prodi' => ['required', 'exists:program_studi,kode_prodi'],
        ];
        $message = [
            'kode_matkul.exists' => 'sorry, we cannot find what are you looking for.'
        ];
        $validator = Validator::make($request->all(), $rules, $message);
        if ($validator->fails()) {
            $response = [
                'status' => 400,
                'message' => $validator->errors()
            ];
            return response()->json($response, $response['status']);
        }

        // key accepted
        $accepted_key = collect($rules)->except('id')->keys();
        $update = collect($request->all())->only($accepted_key);

        try {
            $mata_kuliah = mata_kuliah::find($request->id);
            $update->map(function ($item, $key) use ($mata_kuliah) {
                $mata_kuliah[$key] = $item;
            });
            $mata_kuliah->save();

            $response = [
                'status' => 200,
                'message' => 'Mata kuliah ' . $mata_kuliah->nama_matkul . ' (' . $mata_kuliah->kode_matkul . ') berhasil diubah.'
            ];

            if (!$mata_kuliah->getChanges()) {
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
            'id' => ['required', 'exists:mata_kuliah,id,deleted_at,NULL']
        ];
        $message = [
            'id.exists' => 'sorry, we cannot find what are you looking for.'
        ];
        $validator = Validator::make($request->all(), $rules, $message);
        if ($validator->fails()) {
            $response = [
                'status' => 400,
                'message' => $validator->errors()
            ];
            return response()->json($response, $response['status']);
        }

        try {
            $mata_kuliah = mata_kuliah::find($request->id);
            $mata_kuliah->delete();

            $response = [
                'status' => 200,
                'message' => 'Mata kuliah ' . $mata_kuliah->nama_matkul . ' (' . $mata_kuliah->kode_matkul . ') berhasil dihapus.'
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
