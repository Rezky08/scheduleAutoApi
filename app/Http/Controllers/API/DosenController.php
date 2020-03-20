<?php


namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Dosen as dosen;
use Illuminate\Support\Facades\Validator;

class DosenController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // check apakah ada request kode_dosen
        if ($request->kode_dosen) {
            return $this->show($request);
        }

        try {
            $dosen = dosen::all();
            $response = [
                'status' => 200,
                'data' => $dosen->toArray()
            ];
            return response()->json($response, 200);
        } catch (\Throwable $e) {
            $response = [
                'status' => 500,
                'message' => $e
            ];
            return response()->json($response, 500);
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
            'kode_dosen' => ['required', 'unique:dosen,kode_dosen,NULL,id,deleted_at,NULL'],
            'nama_dosen' => ['required'],
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $response = [
                'status' => 400,
                'message' => $validator->errors()
            ];
            return response()->json($response, 400);
        }
        $insertToDB = [
            'kode_dosen' => $request->kode_dosen,
            'nama_dosen' => $request->nama_dosen,
            'created_at' => now(),
            'updated_at' => now()
        ];
        try {
            dosen::insert($insertToDB);
        } catch (\Throwable $e) {
            $response = [
                'status' => 500,
                'message' => $e
            ];
            return response()->json($response, 500);
        }

        $response = [
            'status' => 200,
            'message' => 'Dosen ' . $request->nama_dosen . ' (' . $request->kode_dosen . ') Berhasil ditambahkan'
        ];
        return response()->json($response, 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $rules = [
            'kode_dosen' => ['required', 'exists:dosen,kode_dosen,deleted_at,NULL']
        ];
        $message = [
            'kode_dosen.exists' => 'sorry, we cannot find what are you looking for.'
        ];
        $validator = Validator::make($request->all(), $rules, $message);
        if ($validator->fails()) {
            $response = [
                'status' => 400,
                'message' => $validator->errors()
            ];
            return response()->json($response, 400);
        }

        $dosen = dosen::where('kode_dosen', $request->kode_dosen)->first();
        $response = [
            'status' => 200,
            'data' => $dosen
        ];
        return response()->json($response, 200);
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
            'kode_dosen' => ['required', 'exists:dosen,kode_dosen,deleted_at,NULL'],
            'kode_dosen_new' => ['sometimes', 'required', 'different:kode_dosen', 'unique:dosen,kode_dosen,' . $request->kode_dosen_new . ',kode_dosen,deleted_at,NULL'],
            'nama_dosen' => ['required'],
        ];
        $message = [
            'kode_dosen.exists' => 'sorry, we cannot find what are you looking for.'
        ];
        $validator = Validator::make($request->all(), $rules, $message);
        if ($validator->fails()) {
            $response = [
                'status' => 400,
                'message' => $validator->errors()
            ];
            return response()->json($response, 400);
        }


        $updated = [
            'kode_dosen' => $request->kode_dosen,
            'nama_dosen' => $request->nama_dosen,
            'updated_at' => now()
        ];
        if ($request->kode_dosen_new) {
            $updated['kode_dosen'] = $request->kode_dosen_new;
        }
        $where = [
            'kode_dosen' => $request->kode_dosen
        ];

        try {
            $dosen = dosen::where($where);
            $res = $dosen->update($updated);
            if (!$res) {
                $response = [
                    'status' => 200,
                    'message' => 'Tidak ada perubahan'
                ];
                return response()->json($response, 200);
            }
            $response = [
                'status' => 200,
                'message' => 'Dosen dengan kode ' . $request->kode_dosen . ' berhasil diubah'
            ];
            return response()->json($response, 200);
        } catch (\Throwable $e) {
            $response = [
                'status' => 500,
                'message' => $e
            ];
            return response()->json($response, 500);
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
            'kode_dosen' => ['required', 'exists:dosen,kode_dosen,deleted_at,NULL']
        ];
        $message = [
            'kode_dosen.exists' => 'sorry, we cannot find what are you looking for.'
        ];
        $validator = Validator::make($request->all(), $rules, $message);
        if ($validator->fails()) {
            $response = [
                'status' => 400,
                'message' => $validator->errors()
            ];
            return response()->json($response, 400);
        }

        try {
            $where = [
                'kode_dosen' => $request->kode_dosen
            ];
            $dosen = dosen::where($where);
            $dosen->delete();

            $response = [
                'status' => 200,
                'message' => 'Dosen dengan Kode ' . $request->kode_dosen . ' berhasil dihapus.'
            ];
            return response()->json($response, 200);
        } catch (\Throwable $e) {
            $response = [
                'status' => 500,
                'message' => $e
            ];
            return response()->json($response, 500);
        }
    }
}
