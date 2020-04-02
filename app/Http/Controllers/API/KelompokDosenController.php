<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Controllers\TemplateController;
use App\KelompokDosen;
use App\Rules\table_column;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class KelompokDosenController extends Controller
{
    protected $template = null;
    protected $model = null;
    public function __construct()
    {
        $this->model = new KelompokDosen();
        $this->template = new TemplateController($this->model, 'kelompok_dosen');
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
            'peminat_id' => ['required', 'exists:peminat,id,deleted_at,NULL']
        ];
        $responseMessage = [
            'success' => 'Kelompok Dosen Menggunakan Peminat :modelData.peminat_id Berhasil ditambahkan'
        ];
        return $this->template->store($request, $rules, [], $responseMessage);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\KelompokDosen  $kelompokDosen
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
     * @param  \App\KelompokDosen  $kelompokDosen
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $rules = [
            'id' => ['required', 'exists:kelompok_dosen,id,deleted_at,NULL'],
            'peminat_id' => ['required', 'exists:peminat,id,deleted_at,NULL']
        ];
        $responseMessage = [
            'success' => 'Kelompok Dosen Menggunakan Peminat :modelData.peminat_id Berhasil diubah'
        ];
        return $this->template->update($request, $rules, [], $responseMessage);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\KelompokDosen  $kelompokDosen
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {

        $rules = [
            'id' => ['required', 'exists:kelompok_dosen,id,deleted_at,NULL']
        ];
        $responseMessage = [
            'success' => 'Kelompok Dosen Menggunakan Peminat :modelData.peminat_id Berhasil dihapus'
        ];
        return $this->template->destroy($request, $rules, [], $responseMessage);
    }
}
