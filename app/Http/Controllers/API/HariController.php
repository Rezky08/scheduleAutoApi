<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Hari as hari;
use Illuminate\Http\Request;

class HariController extends Controller
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
            $hari = hari::all();
            $response = [
                'status' => 200,
                'data' => $hari
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
            'nama_hari' => ['required'],
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
            'nama_hari' => $request->nama_hari,
            'created_at' => now(),
            'updated_at' => now()
        ];
        try {
            hari::insert($insertToDB);
        } catch (\Throwable $e) {
            $response = [
                'status' => 500,
                'message' => $e
            ];
            return response()->json($response, 500);
        }

        $response = [
            'status' => 200,
            'message' => 'hari kuliah ' . $request->nama_hari . ' Berhasil ditambahkan'
        ];
        return response()->json($response, 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Hari  $hari
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $rules = [
            'id' => ['required', 'exists:hari,id']
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

        $hari = hari::where('id', $request->id)->first();
        $response = [
            'status' => 200,
            'data' => $hari
        ];
        return response()->json($response, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Hari  $hari
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $rules = [
            'id' => ['required', 'exists:hari,id'],
            'nama_hari' => ['required'],
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
            'nama_hari' => $request->nama_hari,
            'updated_at' => now()
        ];
        $where = [
            'id' => $request->id
        ];

        try {
            $hari = hari::where($where);
            $res = $hari->update($updated);
            if (!$res) {
                $response = [
                    'status' => 200,
                    'message' => 'Tidak ada perubahan'
                ];
                return response()->json($response, 200);
            }
            $response = [
                'status' => 200,
                'message' => 'hari dengan kode ' . $request->id . ' berhasil diubah'
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
     * @param  \App\Hari  $hari
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $data = [
            'id' => $request->id
        ];
        $rules = [
            'id' => ['required', 'exists:hari,id']
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
            $hari = hari::where($where);
            $count = $hari->count();
            if ($count < 1) {
                $response = [
                    'status' => 400,
                    'message' => 'Sorry, we cannot find what are you looking for.'
                ];
                return response()->json($response, 200);
            }

            $hari->delete();

            $response = [
                'status' => 200,
                'message' => 'hari dengan Kode ' . $request->id . ' berhasil dihapus.'
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
