<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Ruang as ruang;
use Illuminate\Http\Request;

class RuangController extends Controller
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
            $ruang = ruang::all();
            $response = [
                'status' => 200,
                'data' => $ruang
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
    public function store(Request $request)
    {

        $rules = [
            'nama_ruang' => ['required'],
            'keterangan' => ['required'],
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
            'nama_ruang' => $request->nama_ruang,
            'keterangan' => $request->keterangan,
            'created_at' => now(),
            'updated_at' => now()
        ];
        try {
            ruang::insert($insertToDB);
        } catch (\Throwable $e) {
            $response = [
                'status' => 500,
                'message' => $e
            ];
            return response()->json($response, 500);
        }

        $response = [
            'status' => 200,
            'message' => 'ruang kuliah ' . $request->nama_ruang . ' Berhasil ditambahkan'
        ];
        return response()->json($response, 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\ruang  $ruang
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {

        $rules = [
            'id' => ['required', 'exists:ruang,id']
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

        $ruang = ruang::where('id', $request->id)->first();
        $response = [
            'status' => 200,
            'data' => $ruang
        ];
        return response()->json($response, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\ruang  $ruang
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $rules = [
            'id' => ['required', 'exists:ruang,id'],
            'nama_ruang' => ['required'],
            'keterangan' => ['required'],
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
            'nama_ruang' => $request->nama_ruang,
            'keterangan' => $request->keterangan,
            'updated_at' => now()
        ];
        $where = [
            'id' => $request->id
        ];

        try {
            $ruang = ruang::where($where);
            $res = $ruang->update($updated);
            if (!$res) {
                $response = [
                    'status' => 200,
                    'message' => 'Tidak ada perubahan'
                ];
                return response()->json($response, 200);
            }
            $response = [
                'status' => 200,
                'message' => 'ruang dengan kode ' . $request->id . ' berhasil diubah'
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
     * @param  \App\ruang  $ruang
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $data = [
            'id' => $request->id
        ];
        $rules = [
            'id' => ['required', 'exists:ruang,id']
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
            $ruang = ruang::where($where);
            $count = $ruang->count();
            if ($count < 1) {
                $response = [
                    'status' => 400,
                    'message' => 'Sorry, we cannot find what are you looking for.'
                ];
                return response()->json($response, 200);
            }

            $ruang->delete();

            $response = [
                'status' => 200,
                'message' => 'ruang dengan Kode ' . $request->id . ' berhasil dihapus.'
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
