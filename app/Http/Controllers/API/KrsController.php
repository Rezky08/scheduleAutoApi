<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Krs;
use App\KrsMataKuliah;
use App\Matakuliah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class KrsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // check apa ada parameter kode_krs
        if ($request->kode_krs) {
            return $this->show($request);
        }

        try {
            $krs = Krs::all();
            $response = [
                'status' => 200,
                'data' => $krs
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
        // Validation Request
        $rules = [
            'file_upload' => ['required', 'file', 'mimetypes:text/csv,text/plain'],
            'tahun_ajar' => ['required'],
            'semester' => ['required', 'in:E,O'],
        ];
        $message = [
            'file_upload.mimes' => 'The :attribute must be a file of type: csv.'
        ];
        $validator = Validator::make($request->all(), $rules, $message);
        if ($validator->fails()) {
            $response = [
                'status' => 400,
                'message' => $validator->errors()
            ];
            return response()->json($response, 400);
        }

        // get content
        $upload_file = $request->file('file_upload');
        $filePath = $upload_file->path();
        $file = fopen($filePath, 'r');
        $header = fgetcsv($file);
        $header = ['kode_matkul', 'jumlah_krs'];
        $records = [];
        while ($record = fgetcsv($file)) {
            $record_ = [];
            foreach ($header as $key => $value) {
                $record_[$value] = $record[$key];
            }
            $records[] = $record_;
        }
        $records = collect($records);

        // check kode matkul
        $kode_matkul = $records->map(function ($item) {
            return $item['kode_matkul'];
        });
        $kode_matkul = $kode_matkul;
        $kode_matkul_check = Matakuliah::whereIn('kode_matkul', $kode_matkul->toArray())->get();
        $kode_matkul_check = $kode_matkul_check->map(function ($item) {
            return $item->kode_matkul;
        });
        $kode_matkul_check = $kode_matkul->diff($kode_matkul_check->toArray());
        if (!($kode_matkul_check->isEmpty())) {
            $response = [
                'status' => 400,
                'message' => 'Kode Mata Kuliah Tidak Ditemukan.',
                'data' => $kode_matkul_check->toArray(),
            ];
            return response()->json($response, 400);
        }

        // insert kode krs
        $data_insert = [
            'kode_krs' => date('Ymd', time()),
            'tahun_ajar' => $request->tahun_ajar,
            'semester' => $request->semester,
            'created_at' => date('Y-m-d H:i:s', time()),
            'updated_at' => date('Y-m-d H:i:s', time())
        ];
        $kode_krs_check = Krs::where('kode_krs', 'like', $data_insert['kode_krs'] . '%')->orderBy('kode_krs', 'desc');
        $kode_krs_check = $kode_krs_check->first();
        if (is_null($kode_krs_check)) {
            $data_insert['kode_krs'] .= '1';
            Krs::insert($data_insert);
        } else {
            $kode_krs = $kode_krs_check->kode_krs;
            $sub = substr($kode_krs, strlen($data_insert['kode_krs']));
            $sub = (int) $sub + 1;
            $data_insert['kode_krs'] .= $sub;
            Krs::insert($data_insert);
        }

        // insert to krs_matakuliah
        $records = $records->map(function ($item, $index) use ($data_insert) {
            $item = [
                'kode_krs' => $data_insert['kode_krs'],
                'kode_matkul' => $item['kode_matkul'],
                'jumlah_krs' => $item['jumlah_krs'],
                'created_at' => $data_insert['created_at'],
                'updated_at' => $data_insert['updated_at']
            ];
            return $item;
        });
        KrsMataKuliah::insert($records->toArray());
        $semester = $request->semester == "E" ? "Genap" : "Ganjil";
        $response = [
            'status' => 200,
            'message' => 'KRS Tahun Ajaran ' . $request->tahun_ajar . ' Semester ' . $semester
        ];
        return response()->json($response, 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $rules = [
            'kode_krs' => ['required', 'exists:krs,kode_krs,deleted_at,NULL']
        ];
        $message = [
            'kode_krs.exists' => 'sorry, we cannot find what are you looking for.'
        ];
        $validator = Validator::make($request->all(), $rules, $message);
        if ($validator->fails()) {
            $response = [
                'status' => 400,
                'message' => $validator->errors()
            ];
            return response()->json($response, 400);
        }

        $krs = Krs::where('kode_krs', $request->kode_krs)->first();
        $response = [
            'status' => 200,
            'data' => $krs->krs_matkul
        ];
        return response()->json($response, 200);
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
