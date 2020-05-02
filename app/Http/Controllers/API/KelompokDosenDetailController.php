<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Controllers\TemplateController;
use App\KelompokDosenDetail;
use App\Rules\unique_with;
use Illuminate\Http\Request;

class KelompokDosenDetailController extends Controller
{
    protected $template = null;
    protected $model = null;
    public function __construct()
    {
        $this->model = new KelompokDosenDetail();
        $this->template = new TemplateController($this->model, 'kelompok_dosen_detail');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $rules = [
            'kelompok_dosen_id' => ['required', 'exists:kelompok_dosen,id,deleted_at,NULL']
        ];
        return $this->template->index($request, $rules);
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
            'kelompok.unique_with' => 'kelompok dosen id, kode matkul, and kelompok must be unique'
        ];
        $rules = [
            'kelompok_dosen_id' => ['required', 'exists:kelompok_dosen,id,deleted_at,NULL'],
            'kode_matkul' => ['required', 'exists:mata_kuliah,kode_matkul,deleted_at,NULL'],
            'kelompok' => ['required', 'max:5', new unique_with('kelompok_dosen_detail,kelompok_dosen_id,' . $request->kelompok_dosen_id . ',kode_matkul,' . $request->kode_matkul . ',kelompok,' . $request->kelompok . ',deleted_at,NULL', null, $message['kelompok.unique_with'])],
            'kapasitas' => ['required', 'numeric'],
            'kode_dosen' => ['required', 'exists:dosen,kode_dosen,deleted_at,NULL']
        ];
        $responseMessage = [
            'success' => 'Kelompok dosen :modelData.kelompok_dosen_id berhasil tambah Mata Kuliah :modelData.mata_kuliah.nama_matkul Dosen :modelData.dosen.nama_dosen'
        ];
        return $this->template->store($request, $rules, [], $responseMessage);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\KelompokDosenDetail  $kelompokDosenDetail
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $rules = [
            'kelompok_dosen_id' => ['required', 'exists:kelompok_dosen,id,deleted_at,NULL']
        ];
        return $this->template->search($request, $rules);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\KelompokDosenDetail  $kelompokDosenDetail
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $rules = [
            'id' => ['required', 'exists:kelompok_dosen_detail,id,deleted_at,NULL'],
            'kelompok_dosen_id' => ['required', 'exists:kelompok_dosen,id,deleted_at,NULL'],
            'kode_matkul' => ['required', 'exists:mata_kuliah,kode_matkul,deleted_at,NULL'],
            'kapasitas' => ['required', 'numeric'],
            'kelompok' => ['required', 'max:5', new unique_with('kelompok_dosen_detail,kelompok_dosen_id,' . $request->kelompok_dosen_id . ',kode_matkul,' . $request->kode_matkul . ',kelompok,' . $request->kelompok . ',deleted_at,NULL', 'id,' . $request->id)],
            'kode_dosen' => ['required', 'exists:dosen,kode_dosen,deleted_at,NULL'],
        ];
        $responseMessage = [
            'success' => 'Kelompok dosen :modelData.kelompok_dosen_id berhasil ubah Mata Kuliah :modelData.mata_kuliah.nama_matkul Dosen :modelData.dosen.nama_dosen'
        ];
        return $this->template->update($request, $rules, [], $responseMessage);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\KelompokDosenDetail  $kelompokDosenDetail
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $rules = [
            'id' => ['required', 'exists:kelompok_dosen_detail,id,deleted_at,NULL']
        ];
        $responseMessage = [
            'success' => 'Kelompok dosen :modelData.kelompok_dosen_id berhasil hapus Mata Kuliah :modelData.mata_kuliah.nama_matkul Dosen :modelData.dosen.nama_dosen'
        ];
        return $this->template->destroy($request, $rules, [], $responseMessage);
    }
}
