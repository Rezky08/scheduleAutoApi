<?php

namespace App\Http\Controllers\API;

use App\DosenMatakuliah;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\TemplateController;
use App\Rules\unique_with;

class DosenMatkulController extends Controller
{
    protected $template = null;
    protected $model = null;
    public function __construct()
    {
        $this->model = new DosenMatakuliah();
        $this->template = new TemplateController($this->model, 'dosen_mata_kuliah');
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
    public function store(Request $request)
    {
        $message = [
            'kode_dosen.kode_matkul.unique' => 'Kode Dosen and Kode Matkul must be unique'
        ];
        $rules = [
            'kode_dosen' => ['required', 'exists:dosen,kode_dosen,deleted_at,NULL'],
            'kode_matkul' => ['required', 'exists:mata_kuliah,kode_matkul,deleted_at,NULL', new unique_with('dosen_mata_kuliah,kode_dosen,' . $request->kode_dosen . ',kode_matkul,' . $request->kode_matkul . ',deleted_at,NULL', null, $message['kode_dosen.kode_matkul.unique'])]
        ];
        $responseMessage = [
            'success' => "berhasil menambahkan :modelData.dosen.nama_dosen mengampu Mata Kuliah :modelData.matkul.nama_matkul"
        ];
        return $this->template->store($request, $rules, [], $responseMessage);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
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
        $message = [
            'kode_dosen.kode_matkul.unique' => 'Kode Dosen and Kode Matkul must be unique'
        ];
        $rules = [
            'id' => ['required', 'exists:dosen_mata_kuliah,id,deleted_at,NULL'],
            'kode_dosen' => ['required', 'exists:dosen,kode_dosen,deleted_at,NULL'],
            'kode_matkul' => ['required', 'exists:mata_kuliah,kode_matkul,deleted_at,NULL', new unique_with('dosen_mata_kuliah,kode_dosen,' . $request->kode_dosen . ',kode_matkul,' . $request->kode_matkul . ',deleted_at,NULL', 'id,' . $request->id, $message['kode_dosen.kode_matkul.unique'])]
        ];
        $responseMessage = [
            'success' => "berhasil mengubah :modelData.dosen.nama_dosen mengampu Mata Kuliah :modelData.matkul.nama_matkul"
        ];
        return $this->template->update($request, $rules, [], $responseMessage);
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
            'id' => ['required', 'exists:dosen_mata_kuliah,id,deleted_at,NULL'],
        ];
        $responseMessage = [
            'success' => "berhasil menghapus :modelData.dosen.nama_dosen mengampu Mata Kuliah :modelData.matkul.nama_matkul"
        ];
        return $this->template->destroy($request, $rules, [], $responseMessage);
    }
}
