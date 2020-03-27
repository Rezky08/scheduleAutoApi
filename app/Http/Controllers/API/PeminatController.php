<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Peminat;
use App\Rules\unique_with;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PeminatController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        // validate input
        $rules = [
            'tahun_ajaran' => ['required', 'regex:/[0-9]{4,4}\/[0-9]{4,4}+$/'],
            'semester' => ['required', 'in:E,O'],
            'tahun_ajaran_semester' => [new unique_with('peminat,tahun_ajaran,' . $request->tahun_ajaran . ',semester,' . $request->semester . ',deleted_at,NULL')]
        ];
        $input_added = ['tahun_ajaran_semester' => null];
        $message = [
            'tahun_ajaran.regex' => "sesuaikan format :attribute dengan:  tahun/tahun"
        ];
        $validator = Validator::make($request->all() + $input_added, $rules, $message);
        if ($validator->fails()) {
            $response = [
                'status' => 400,
                'message' => $validator->errors()
            ];
            return response()->json($response, 400);
        }
        $insertToDB = [
            'tahun_ajaran' => $request->tahun_ajaran,
            'semester' => $request->semester,
            'created_at' => date('Y-m-d H:i:s', time()),
            'updated_at' => date('Y-m-d H:i:s', time()),
        ];
        try {
            $semester = $request->semester == 'O' ? "Genap" : "Ganjil";
            Peminat::insert($insertToDB);
            $response = [
                'status' => 200,
                'message' => "Berhasil Menambahkan Peminat Semester " . $semester . " Tahun Ajaran " . $request->tahun_ajaran . "."
            ];
            return response()->json($response, 400);
        } catch (Exception $e) {
            $response = [
                'status' => 500,
                'message' => "Internal Server Error"
            ];
            return response()->json($response, 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
