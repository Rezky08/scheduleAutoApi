<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Controllers\TemplateController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Matakuliah as mata_kuliah;
use App\Rules\table_column;
use App\Rules\unique_with;
use Exception;

class MatakuliahController extends Controller
{

    protected $template = null;
    public function __construct()
    {
        $model = new mata_kuliah();
        $this->template = new TemplateController($model, 'mata_kuliah');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    // menampilkan semua yang ada dalam table mata kuliah
    public function index(Request $request)
    {
        // check apakah ada request
        if (count($request->all()) > 0) {
            return $this->show($request);
        }
        return $this->template->index($request);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    // menambahkan mata kuliah ke database
    public function store(Request $request)
    {
        $rules = [
            'kode_matkul' => ['required', 'unique:mata_kuliah,kode_matkul,NULL,id,deleted_at,NULL', 'max:10'],
            'sks_matkul' => ['required', 'numeric'],
            'nama_matkul' => ['required'],
            'status_matkul' => ['boolean'],
            'kode_prodi' => ['required', 'exists:program_studi,kode_prodi,deleted_at,NULL'],
        ];
        $responseMessage = [
            'success' => "Mata Kuliah :modelData.nama_matkul ( :modelData.kode_matkul ) berhasil ditambahkan."
        ];
        return $this->template->store($request, $rules, [], $responseMessage);
    }

    /**
     * Display the specified resource.
     *
     * @param  string
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        return $this->template->show($request);
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
            'id' => ['required', 'exists:mata_kuliah,id,deleted_at,NULL'],
            'kode_matkul' => ['required', new unique_with('mata_kuliah,kode_matkul,' . $request->kode_matkul . ',deleted_at,NULL', 'id,' . $request->id)],
            'sks_matkul' => ['required', 'numeric'],
            'nama_matkul' => ['required', 'max:100'],
            'status_matkul' => ['required', 'boolean'],
            'lab_matkul' => ['required', 'boolean'],
            'kode_prodi' => ['required', 'exists:program_studi,kode_prodi'],
        ];
        $message = [
            'kode_matkul.exists' => 'sorry, we cannot find what are you looking for.'
        ];
        $responseMessage = [
            'success' => "Mata Kuliah :modelData.nama_matkul ( :modelData.kode_matkul ) berhasil diubah."
        ];
        return $this->template->update($request, $rules, $message, $responseMessage);
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
            'id' => ['required', 'exists:mata_kuliah,id,deleted_at,NULL']
        ];
        $message = [
            'id.exists' => 'sorry, we cannot find what are you looking for.'
        ];
        $responseMessage = [
            'success' => "Mata Kuliah :modelData.nama_matkul ( :modelData.kode_matkul ) berhasil dihapus."
        ];
        return $this->template->destroy($request, $rules, $message, $responseMessage);
    }
}
