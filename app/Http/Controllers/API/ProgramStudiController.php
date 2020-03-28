<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
// masukin yang diperluin buat file ini
use App\ProgramStudi as program_studi;
use App\Rules\table_column;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ProgramStudiController extends Controller
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
            $program_studi = program_studi::all();
            $response = [
                'status' => 200,
                'data' => $program_studi
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
    // menambahkan program studi ke database
    public function store(Request $request)
    {
        // validasi inputan
        // ngecek ada inputan yang nama nya kode_prodi sama nama_prodi dengan rules begitu
        $rules = [
            'kode_prodi' => ['required', 'unique:program_studi,kode_prodi,NULL,kode_prodi,deleted_at,NULL', 'max:10'],
            'nama_prodi' => ['required'],
            'keterangan_prodi' => ['sometimes', 'required'],
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
            return response()->json($response, $response['status']);
        }
        $insertToDB = [
            'kode_prodi' => $request->kode_prodi,
            'nama_prodi' => $request->nama_prodi,
            'keterangan_prodi' => $request->keterangan_prodi,
            'created_at' => now(),
            'updated_at' => now()
        ];
        try {
            program_studi::insert($insertToDB);
            $response = [
                'status' => 200,
                'message' => 'Program Studi ' . $request->nama_prodi . ' (' . $request->kode_prodi . ') Berhasil ditambahkan'
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
     * @param  \App\ProgramStudi  $programStudi
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $table_column = collect($request->all())->keys()->toArray();
        $rules = [
            'column' => ['required', new table_column('program_studi')]
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
            $program_studi = program_studi::where($request->all())->get();
            $response = [
                'status' => 200,
                'data' => $program_studi
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
     * @param  \App\ProgramStudi  $programStudi
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        // ini paham
        $rules = [
            'id' => ['required', 'exists:program_studi,id,deleted_at,NULL'],
            'kode_prodi' => ['required', Rule::unique('program_studi', 'kode_prodi')->ignore($request->id, 'id')],
            'nama_prodi' => ['required'],
            'keterangan_prodi' => ['sometimes', 'required'],
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
            return response()->json($response, $response['status']);
        }

        // update key only
        $accepted_key = collect($rules)->except('id')->keys();
        $update = collect($request->all())->only($accepted_key);

        try {
            $program_studi = program_studi::find($request->id);
            $update->map(function ($item, $key) use ($program_studi) {
                $program_studi[$key] = $item;
            });
            $program_studi->save();

            $response = [
                'status' => 200,
                'message' => 'Program Studi ' . $program_studi->nama_prodi . ' (' . $program_studi->kode_prodi . ') berhasil diubah'
            ];
            if (!$program_studi->getChanges()) {
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
     * @param  \App\ProgramStudi  $programStudi
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $rules = [
            'id' => ['required', 'exists:program_studi,id,deleted_at,NULL']
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
            $program_studi = program_studi::find($request->id);
            $program_studi->delete();

            $response = [
                'status' => 200,
                'message' => 'Program Studi ' . $program_studi->nama_prodi . ' (' . $program_studi->kode_prodi . ') berhasil dihapus'
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
