<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Jam as jam;
use Illuminate\Http\Request;

class JamController extends Controller
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
            $jam = jam::all();
            $response = [
                'status' => 200,
                'data' => $jam
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
            'jam_mulai' => ['required', 'date_format:H:i:s'],
            'jam_selesai' => ['required', 'date_format:H:i:s'],
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
            'jam_mulai' => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai,
            'created_at' => now(),
            'updated_at' => now()
        ];
        try {
            jam::insert($insertToDB);
        } catch (\Throwable $e) {
            $response = [
                'status' => 500,
                'message' => $e
            ];
            return response()->json($response, 500);
        }

        $response = [
            'status' => 200,
            'message' => 'Jam kuliah ' . $request->jam_mulai . ' s/d ' . $request->jam_selesai . ' Berhasil ditambahkan'
        ];
        return response()->json($response, 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Jam  $jam
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {

        $rules = [
            'id' => ['required', 'exists:jam,id']
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

        $jam = jam::where('id', $request->id)->first();
        $response = [
            'status' => 200,
            'data' => $jam
        ];
        return response()->json($response, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Jam  $jam
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $rules = [
            'id' => ['required', 'exists:jam,id'],
            'jam_mulai' => ['required', 'date_format:H:i:s'],
            'jam_selesai' => ['required', 'date_format:H:i:s'],
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
            'jam_mulai' => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai,
            'updated_at' => now()
        ];
        $where = [
            'id' => $request->id
        ];

        try {
            $jam = jam::where($where);
            $res = $jam->update($updated);
            if (!$res) {
                $response = [
                    'status' => 200,
                    'message' => 'Tidak ada perubahan'
                ];
                return response()->json($response, 200);
            }
            $response = [
                'status' => 200,
                'message' => 'Jam dengan kode ' . $request->id . ' berhasil diubah'
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
     * @param  \App\Jam  $jam
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $data = [
            'id' => $request->id
        ];
        $rules = [
            'id' => ['required', 'exists:jam,id']
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
            $jam = jam::where($where);
            $count = $jam->count();
            if ($count < 1) {
                $response = [
                    'status' => 400,
                    'message' => 'Sorry, we cannot find what are you looking for.'
                ];
                return response()->json($response, 200);
            }

            $jam->delete();

            $response = [
                'status' => 200,
                'message' => 'Jam dengan Kode ' . $request->id . ' berhasil dihapus.'
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
