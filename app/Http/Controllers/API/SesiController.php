<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Sesi as sesi;
use Illuminate\Http\Request;

class SesiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // check apa ada parameter id
        if ($request->id) {
            return $this->show($request);
        }

        try {
            $sesi = sesi::all();
            $response = [
                'status' => 200,
                'data' => $sesi
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
            'sesi_mulai' => ['required', 'date_format:H:i:s'],
            'sesi_selesai' => ['required', 'date_format:H:i:s'],
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
            'sesi_mulai' => $request->sesi_mulai,
            'sesi_selesai' => $request->sesi_selesai,
            'created_at' => now(),
            'updated_at' => now()
        ];
        try {
            sesi::insert($insertToDB);
        } catch (\Throwable $e) {
            $response = [
                'status' => 500,
                'message' => $e
            ];
            return response()->json($response, 500);
        }

        $response = [
            'status' => 200,
            'message' => 'Sesi kuliah ' . $request->sesi_mulai . ' s/d ' . $request->sesi_selesai . ' Berhasil ditambahkan'
        ];
        return response()->json($response, 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Sesi  $sesi
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {

        $rules = [
            'id' => ['required', 'exists:sesi,id,deleted_at,NULL']
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
            return response()->json($response, 400);
        }

        $sesi = sesi::where('id', $request->id)->first();
        $response = [
            'status' => 200,
            'data' => $sesi
        ];
        return response()->json($response, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Sesi  $sesi
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $rules = [
            'id' => ['required', 'exists:sesi,id,deleted_at,NULL'],
            'sesi_mulai' => ['required', 'date_format:H:i:s'],
            'sesi_selesai' => ['required', 'date_format:H:i:s'],
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
            return response()->json($response, 400);
        }

        $updated = [
            'sesi_mulai' => $request->sesi_mulai,
            'sesi_selesai' => $request->sesi_selesai,
            'updated_at' => now()
        ];
        $where = [
            'id' => $request->id
        ];

        try {
            $sesi = sesi::where($where);
            $res = $sesi->update($updated);
            if (!$res) {
                $response = [
                    'status' => 200,
                    'message' => 'Tidak ada perubahan'
                ];
                return response()->json($response, 200);
            }
            $response = [
                'status' => 200,
                'message' => 'Sesi dengan kode ' . $request->id . ' berhasil diubah'
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
     * @param  \App\Sesi  $sesi
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $data = [
            'id' => $request->id
        ];
        $rules = [
            'id' => ['required', 'exists:sesi,id,deleted_at,NULL']
        ];
        $message = [
            'id.exists' => 'sorry, we cannot find what are you looking for.'
        ];
        $validator = Validator::make($data, $rules, $message);
        if ($validator->fails()) {
            $response = [
                'status' => 400,
                'message' => $validator->errors()
            ];
            return response()->json($response, 400);
        }

        try {
            $where = [
                'id' => $request->id
            ];
            $sesi = sesi::where($where);
            $count = $sesi->count();
            if ($count < 1) {
                $response = [
                    'status' => 400,
                    'message' => 'Sorry, we cannot find what are you looking for.'
                ];
                return response()->json($response, 200);
            }

            $sesi->delete();

            $response = [
                'status' => 200,
                'message' => 'Sesi dengan Kode ' . $request->id . ' berhasil dihapus.'
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
