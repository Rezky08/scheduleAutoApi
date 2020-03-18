<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
// masukin yang diperluin buat file ini
use App\ProgramStudi as program_studi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProgramStudiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        // check apa ada parameter id
        if ($request->kode_prodi) {
            return $this->show($request);
        }

        try {
            $program_studi = program_studi::all();
            $response = [
                'status' => 200,
                'data' => $program_studi
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
    // menambahkan program studi ke database
    public function store(Request $request)
    {
        // validasi inputan
        // ngecek ada inputan yang nama nya kode_prodi sama nama_prodi dengan rules begitu
        $rules = [
            'kode_prodi' => ['required', 'unique:program_studi,kode_prodi', 'max:10'],
            'nama_prodi' => ['required']
        ];
        // rules nya cek di web laravel aja

        // Validator itu semacam package harus di 'use' di awal
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            // kalo inputanya ga sesuai rules, dia bakal balikin error
            $response = [
                'status' => 400,
                'message' => $validator->errors()
            ];
            return response()->json($response, 400);
        }
        $insertToDB = [
            'kode_prodi' => $request->kode_prodi,
            'nama_prodi' => $request->nama_prodi,
            'created_at' => now(),
            'updated_at' => now()
        ];
        try {
            program_studi::insert($insertToDB);
        } catch (\Throwable $e) {
            $response = [
                'status' => 500,
                'message' => $e
            ];
            return response()->json($response, 500);
        }

        $response = [
            'status' => 200,
            'message' => 'Program Studi ' . $request->nama_prodi . ' (' . $request->kode_prodi . ') Berhasil ditambahkan'
        ];
        return response()->json($response, 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\ProgramStudi  $programStudi
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        // Validasi apakah ada inputan bernama kode_prodi atau tidak
        $rules = [
            'kode_prodi' => ['required', 'exists:program_studi,kode_prodi']
        ];
        $message = [
            'kode_prodi.exists' => 'sorry, we cannot find what are you looking for.'
        ];
        $validator = Validator::make($request->all(), $rules, $message);
        if ($validator->fails()) {
            $response = [
                'status' => 400,
                'message' => $validator->errors()
            ];
            return response()->json($response, 400);
        }

        $program_studi = program_studi::where('kode_prodi', $request->kode_prodi)->first();
        $response = [
            'status' => 200,
            'data' => $program_studi
        ];
        return response()->json($response, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\ProgramStudi  $programStudi
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        // ini paham
        $rules = [
            'kode_prodi' => ['required', 'exists:program_studi,kode_prodi'],
            'kode_prodi_new' => ['sometimes', 'required', 'different:kode_prodi', 'unique:program_studi,kode_prodi', 'max:10'],
            'nama_prodi' => ['required'],
            'keterangan_prodi' => ['sometimes', 'required'],
            'kode_prodi' => ['required', 'exists:program_studi,kode_prodi'], // ini itu nagmbil dari tabel lain kan?
        ];
        $message = [
            'id.exists' => 'sorry, we cannot find what are you looking for.'
        ];
        // yg ini yg tadi lu bilang ngecek itu kan . iya
        $validator = Validator::make($request->all(), $rules, $message);
        if ($validator->fails()) {
            $response = [
                'status' => 400,
                'message' => $validator->errors()
            ];
            return response()->json($response, 400);
        }

        //ini seharusnya proses updatenya ya soalnya ada wherenya kek semacam query di laravel
        // ini cuma persiapan nya, yang kiri nama kolom nya yang kanan isinya
        $updated = [
            'kode_prodi' => $request->kode_prodi,
            'nama_prodi' => $request->nama_prodi,
            'keterangan_prodi' => $request->keterangan_prodi,
            'updated_at' => now()
        ];
        // update dimana matkul yang di iinput
        $where = [
            'kode_prodi' => $request->kode_prodi
        ];
        // apa inih?
        // querynya dijalanin disini
        try {
            $program_studi = program_studi::where($where);
            $res = $program_studi->update($updated);
            if (!$res) {
                $response = [
                    'status' => 200,
                    'message' => 'Tidak ada perubahan'
                ];
                return response()->json($response, 200);
            }
            $response = [
                'status' => 200,
                'message' => 'Program Studi dengan kode ' . $request->kode_prodi . ' berhasil diubah'
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
     * @param  \App\ProgramStudi  $programStudi
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $rules = [
            'kode_prodi' => ['required', 'exists:program_studi,kode_prodi']
        ];
        $message = [
            'kode_prodi.exists' => 'sorry, we cannot find what are you looking for.'
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
                'kode_prodi' => $request->kode_prodi
            ];
            $program_studi = program_studi::where($where);
            $count = $program_studi->count();
            if ($count < 1) {
                $response = [
                    'status' => 400,
                    'message' => 'Sorry, we cannot find what are you looking for.'
                ];
                return response()->json($response, 200);
            }

            $program_studi->delete();

            $response = [
                'status' => 200,
                'message' => 'Program Studi dengan Kode ' . $request->kode_prodi . ' berhasil dihapus.'
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
