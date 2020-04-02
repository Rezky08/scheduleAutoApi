<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Controllers\TemplateController;
use App\JadwalDetail;
use App\Rules\exists_with;
use Illuminate\Http\Request;

class JadwalDetailController extends Controller
{

    protected $template = null;
    protected $model = null;
    public function __construct()
    {
        $this->model = new JadwalDetail();
        $this->template = new TemplateController($this->model, 'jadwal_detail');
    }

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
        $rules = [
            'kode_matkul' => ['required', 'exists:mata_kuliah,kode_matkul,deleted_at,NULL'],
            'nama_matkul' => ['required', 'exists:mata_kuliah,nama_matkul,deleted_at,NULL'],
            'sks_matkul' => ['required', 'numeric'],
            'kelompok' => ['required', 'max:5'],
            'kode_dosen' => ['required', new exists_with('dosen,kode_dosen,' . $request->kode_dosen . ',nama_dosen,' . $request->nama_dosen . ',deleted_at,NULL')],
            'nama_dosen' => ['required', new exists_with('dosen,kode_dosen,' . $request->kode_dosen . ',nama_dosen,' . $request->nama_dosen . ',deleted_at,NULL')],
            'ruang' => ['required', 'exists:ruang,nama_ruang,deleted_at,NULL'],
            'hari' => ['required', 'exists:hari,nama_hari,deleted_at,NULL'],
            'sesi_mulai' => ['required', 'exists:sesi,sesi_mulai,deleted_at,NULL'],
            'sesi_selesai' => ['required', 'exists:sesi,sesi_selesai,deleted_at,NULL']
        ];
        $messageResponse = [
            'success' => 'Jadwal Mata Kuliah :modelData.nama_matkul Dosen :modelData.nama_dosen Berhasil ditambahkan'
        ];
        return $this->template->store($request, $rules, [], $messageResponse);
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
        $rules = [
            'id' => ['required', 'exists:jadwal_detail,id,deleted_at,NULL'],
            'kode_matkul' => ['required', 'exists:mata_kuliah,kode_matkul,deleted_at,NULL'],
            'nama_matkul' => ['required', 'exists:mata_kuliah,nama_matkul,deleted_at,NULL'],
            'sks_matkul' => ['required', 'numeric'],
            'kelompok' => ['required', 'max:5'],
            'kode_dosen' => ['required', new exists_with('dosen,kode_dosen,' . $request->kode_dosen . ',nama_dosen,' . $request->nama_dosen . ',deleted_at,NULL')],
            'nama_dosen' => ['required', new exists_with('dosen,kode_dosen,' . $request->kode_dosen . ',nama_dosen,' . $request->nama_dosen . ',deleted_at,NULL')],
            'ruang' => ['required', 'exists:ruang,nama_ruang,deleted_at,NULL'],
            'hari' => ['required', 'exists:hari,nama_hari,deleted_at,NULL'],
            'sesi_mulai' => ['required', 'exists:sesi,sesi_mulai,deleted_at,NULL'],
            'sesi_selesai' => ['required', 'exists:sesi,sesi_selesai,deleted_at,NULL']
        ];
        $messageResponse = [
            'success' => 'Jadwal id :modelData.id Mata Kuliah :modelData.nama_matkul Dosen :modelData.nama_dosen Berhasil diubah'
        ];
        return $this->template->update($request, $rules, [], $messageResponse);
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
            'id' => ['required', 'exists:jadwal_detail,id,deleted_at,NULL']
        ];
        $messageResponse = [
            'success' => 'Jadwal id :modelData.id Mata Kuliah :modelData.nama_matkul Dosen :modelData.nama_dosen Berhasil dihapus'
        ];
        return $this->template->destroy($request, $rules, [], $messageResponse);
    }
}
