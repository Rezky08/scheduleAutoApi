<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Rules\table_column;
use Illuminate\Support\Facades\Validator;
use App\Sesi as sesi;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SesiController extends Controller
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
            $sesi = sesi::all();
            $response = [
                'status' => 200,
                'data' => $sesi
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
        $rules = [
            'sesi_mulai' => ['required', 'date_format:H:i:s', 'unique:sesi,sesi_mulai,deleted_at,NULL'],
            'sesi_selesai' => ['required', 'date_format:H:i:s', 'unique:sesi,sesi_selesai,deleted_at,NULL'],
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
            'sesi_mulai' => $request->sesi_mulai,
            'sesi_selesai' => $request->sesi_selesai,
            'created_at' => now(),
            'updated_at' => now()
        ];
        try {
            sesi::insert($insertToDB);
            $response = [
                'status' => 200,
                'message' => 'Sesi kuliah ' . $request->sesi_mulai . ' s/d ' . $request->sesi_selesai . ' Berhasil ditambahkan'
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
     * @param  \App\Sesi  $sesi
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $table_column = collect($request->all())->keys()->toArray();
        $rules = [
            'column' => ['required', new table_column('sesi')]
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
            $sesi = sesi::where($request->all())->get();
            $response = [
                'status' => 200,
                'data' => $sesi
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
     * @param  \App\Sesi  $sesi
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $rules = [
            'id' => ['required', 'exists:sesi,id,deleted_at,NULL'],
            'sesi_mulai' => ['required', 'date_format:H:i:s', Rule::unique('sesi', 'sesi_mulai')->ignore($request->id, 'id')],
            'sesi_selesai' => ['required', 'date_format:H:i:s', Rule::unique('sesi', 'sesi_selesai')->ignore($request->id, 'id')],
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

        // update key only
        $accepted_key = collect($rules)->except('id')->keys();
        $update = collect($request->all())->only($accepted_key);

        try {
            $sesi = sesi::find($request->id);
            $update->map(function ($item, $key) use ($sesi) {
                $sesi[$key] = $item;
            });
            $sesi->save();

            $response = [
                'status' => 200,
                'message' => 'Sesi dengan kode ' . $request->id . ' berhasil diubah'
            ];

            if (!$sesi->getChanges()) {
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
     * @param  \App\Sesi  $sesi
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
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
            return response()->json($response, $response['status']);
        }

        try {
            $sesi = sesi::find($request->id);
            $sesi->delete();

            $response = [
                'status' => 200,
                'message' => 'Sesi dengan Kode ' . $request->id . ' berhasil dihapus.'
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
