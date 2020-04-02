<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Controllers\TemplateController;
use App\Peminat;
use App\PeminatDetail;
use App\Rules\table_column;
use App\Rules\unique_with;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class PeminatDetailController extends Controller
{
    protected $template = null;
    protected $model = null;
    public function __construct()
    {
        $this->model = new PeminatDetail();
        $this->template = new TemplateController($this->model, 'peminat_detail');
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
        // validasi input
        $rules = [
            'peminat_id' => ['required', 'exists:peminat,id,deleted_at,NULL'],
            'kode_matkul' => ['required', 'exists:mata_kuliah,kode_matkul,deleted_at,NULL', new unique_with('peminat_detail,peminat_id,' . $request->peminat_id . ',kode_matkul,' . $request->kode_matkul . ',deleted_at,NULL')],
            'jumlah_peminat' => ['required', 'numeric']
        ];
        $responseMessage = [
            'success' => 'Berhasil Menambahkan Peminat Mata Kuliah :modelData.mata_kuliah.nama_matkul ( :modelData.jumlah_peminat ) Tahun Ajaran :modelData.peminat.tahun_ajaran Semester :modelData.peminat.semester_detail.keterangan '
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
        $peminat_detail = PeminatDetail::find($request->id);
        try {
            $request->request->add(['peminat_id' => $peminat_detail->peminat_id]);
        } catch (Exception $e) {
        }

        // validasi input
        $rules = [
            'id' => ['bail', 'required', 'exists:peminat_detail,id,deleted_at,NULL'],
            'kode_matkul' => ['required', 'exists:mata_kuliah,kode_matkul,deleted_at,NULL', new unique_with('peminat_detail,peminat_id,' . $request->peminat_id . ',kode_matkul,' . $request->kode_matkul . ',deleted_at,NULL', 'id,' . $request->id)],
            'jumlah_peminat' => ['required', 'numeric']
        ];
        $responseMessage = [
            'success' => 'Berhasil Mengubah Peminat Mata Kuliah :modelData.mata_kuliah.nama_matkul ( :modelData.jumlah_peminat ) Tahun Ajaran :modelData.peminat.tahun_ajaran Semester :modelData.peminat.semester_detail.keterangan '
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
            'id' => ['required', 'exists:peminat_detail,id,deleted_at,NULL']
        ];
        $responseMessage = [
            'success' => 'Berhasil Menghapus Peminat Mata Kuliah :modelData.mata_kuliah.nama_matkul ( :modelData.jumlah_peminat ) Tahun Ajaran :modelData.peminat.tahun_ajaran Semester :modelData.peminat.semester_detail.keterangan '
        ];
        return $this->template->destroy($request, $rules, [], $responseMessage);
    }
}
