<?php


namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Dosen as dosen;
use App\Rules\table_column;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class DosenController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // check apakah ada request
        if (count($request->all()) > 0) {
            return $this->show($request);
        }

        try {
            $dosen = dosen::all();
            $response = [
                'status' => 200,
                'data' => $dosen->toArray()
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
            'kode_dosen' => ['required', 'unique:dosen,kode_dosen,NULL,id,deleted_at,NULL'],
            'nama_dosen' => ['required'],
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
            'kode_dosen' => $request->kode_dosen,
            'nama_dosen' => $request->nama_dosen,
            'created_at' => now(),
            'updated_at' => now()
        ];
        try {
            dosen::insert($insertToDB);
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

        $response = [
            'status' => 200,
            'message' => 'Dosen ' . $request->nama_dosen . ' (' . $request->kode_dosen . ') Berhasil ditambahkan'
        ];
        return response()->json($response, $response['status']);
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
            'column' => ['required', new table_column('dosen')]
        ];
        $validator = Validator::make($request->all() + ['column' => $table_column], $rules);
        if ($validator->fails()) {
            $response = [
                'status' => 400,
                'message' => $validator->errors()
            ];
            return response()->json($response, $response['status']);
        }

        $whereCond = collect($request->all());
        $whereCond = $whereCond->map(function ($item) {
            return $item;
        });

        try {
            $dosen = dosen::where($request->all())->get();
            $response = [
                'status' => 200,
                'data' => $dosen
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
        $rules = [
            'id' => ['required', 'exists:dosen,id,deleted_at,NULL'],
            'kode_dosen' => ['required', Rule::unique('dosen', 'kode_dosen')->ignore($request->id, 'id')],
            'nama_dosen' => ['required', 'max:100'],
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
            return response()->json($response, $response['status']);
        }

        // key accepted
        $accepted_key = ['kode_dosen', 'nama_dosen'];
        $update = collect($request->all())->only($accepted_key);

        try {
            $dosen = dosen::find($request->id);
            $update->map(function ($item, $key) use ($dosen) {
                $dosen[$key] = $item;
            });
            $dosen->save();

            $response = [
                'status' => 200,
                'message' => 'Dosen ' . $dosen->nama_dosen . ' (' . $dosen->kode_dosen . ') berhasil diubah'
            ];

            if (!$dosen->getChanges()) {
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
            'id' => ['required', 'exists:dosen,id,deleted_at,NULL']
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
            $dosen = dosen::find($request->id);
            $dosen->delete();

            $response = [
                'status' => 200,
                'message' => 'Dosen ' . $dosen->nama_dosen . ' (' . $dosen->kode_dosen . ') berhasil dihapus.'
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
