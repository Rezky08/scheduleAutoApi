<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Controllers\TemplateController;
// masukin yang diperluin buat file ini
use App\ProgramStudi as program_studi;
use App\Rules\table_column;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ProgramStudiController extends Controller
{
    protected $template = null;
    protected $model = null;
    public function __construct()
    {
        $this->model = new program_studi();
        $this->template = new TemplateController($this->model, 'program_studi', []);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        return $this->template->index($request);
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
        $rules = [
            'kode_prodi' => ['required', 'unique:program_studi,kode_prodi,NULL,kode_prodi,deleted_at,NULL', 'max:10'],
            'nama_prodi' => ['required', 'unique:program_studi,nama_prodi,NULL,nama_prodi,deleted_at,NULL'],
            'keterangan_prodi' => ['sometimes', 'required'],
        ];
        $responseMessage = [
            'success' => 'Program Studi :modelData.nama_prodi ( :modelData.kode_prodi ) Berhasil Ditambahkan'
        ];
        return $this->template->store($request, $rules, [], $responseMessage);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\ProgramStudi  $programStudi
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        return $this->template->search($request);
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
            'nama_prodi' => ['required', Rule::unique('program_studi', 'nama_prodi')->ignore($request->id, 'id')],
            'keterangan_prodi' => ['sometimes', 'required'],
        ];
        $message = [
            'id.exists' => 'sorry, we cannot find what are you looking for.'
        ];
        $responseMessage = [
            'success' => 'Program Studi :modelData.nama_prodi ( :modelData.kode_prodi ) Berhasil diubah'
        ];
        return $this->template->update($request, $rules, $message, $responseMessage);
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
        $responseMessage = [
            'success' => 'Program Studi :modelData.nama_prodi ( :modelData.kode_prodi ) Berhasil dihapus'
        ];
        return $this->template->destroy($request, $rules, $message, $responseMessage);
    }
}
