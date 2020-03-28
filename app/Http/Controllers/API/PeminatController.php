<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Peminat;
use App\Rules\table_column;
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
    public function index(Request $request)
    {
        if (count($request->all()) > 0) {
            return $this->show($request);
        }
        try {
            $peminat = Peminat::all();
            $response = [
                'status' => 200,
                'data' => $peminat
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
            return response()->json($response, $response['status']);
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
            return response()->json($response, $response['status']);
        } catch (Exception $e) {
            $response = [
                'status' => 500,
                'message' => "Internal Server Error"
            ];
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
            'column' => ['required', new table_column('peminat')]
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
            $peminat = Peminat::where($request->all())->get();
            $response = [
                'status' => 200,
                'data' => $peminat
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
        $peminat = Peminat::find($request->id);
        try {
            $request->request->add(['peminat_id' => $peminat->peminat_id]);
        } catch (Exception $e) {
        }

        // validasi input
        $message = [
            'tahun_ajaran.unique_with' => 'Tahun Ajaran Semester already has been taken.'
        ];
        $rules = [
            'id' => ['bail', 'required', 'exists:peminat,id,deleted_at,NULL'],
            'tahun_ajaran' => ['required', 'regex:/[0-9]{4,4}\/[0-9]{4,4}+$/', new unique_with('peminat,tahun_ajaran,' . $request->tahun_ajaran . ',semester,' . $request->semester . ',deleted_at,NULL', 'id,' . $request->id, $message['tahun_ajaran.unique_with'])],
            'semester' => ['required', 'in:E,O'],
        ];
        $validator = Validator::make($request->all(), $rules);
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
            $peminat = Peminat::find($request->id);
            $update->map(function ($item, $key) use ($peminat) {
                $peminat[$key] = $item;
            });
            $peminat->save();
            $response = [
                'status' => 200,
                'message' => "Berhasil Mengubah Peminat Tahun Ajaran " . $peminat->tahun_ajaran . " Semester " . $peminat->semester_detail->keterangan
            ];
            if (!$peminat->getChanges()) {
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
            'id' => ['required', 'exists:peminat,id,deleted_at,NULL']
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
            $peminat = Peminat::find($request->id);
            $peminat->delete();
            $response = [
                'status' => 200,
                'message' => "Berhasil Menghapus Peminat Tahun Ajaran " . $peminat->tahun_ajaran . " Semester " . $peminat->semester_detail->keterangan
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
