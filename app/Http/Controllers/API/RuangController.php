<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Controllers\TemplateController;
use Illuminate\Support\Facades\Validator;
use App\Ruang as ruang;
use App\Rules\table_column;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RuangController extends Controller
{
    protected $template = null;
    public function __construct()
    {
        $model = new ruang();
        $this->template = new TemplateController($model, 'ruang');
    }
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
        return $this->template->index($request);
    }

    public function store(Request $request)
    {

        $rules = [
            'nama_ruang' => ['required', 'unique:ruang,nama_ruang,NULL,id,deleted_at,NULL'],
            'keterangan' => ['required'],
        ];
        $responseMessage = [
            'success' => "Ruang :modelData.nama_ruang Berhasil ditambahkan"
        ];
        return $this->template->store($request, $rules, [], $responseMessage);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\ruang  $ruang
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
     * @param  \App\ruang  $ruang
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $rules = [
            'id' => ['required', 'exists:ruang,id,deleted_at,NULL'],
            'nama_ruang' => ['required', Rule::unique('ruang', 'nama_ruang')->ignore($request->id, 'id')],
            'keterangan' => ['required'],
        ];
        $message = [
            'id.exists' => 'sorry, we cannot find what are you looking for.'
        ];
        $responseMessage = [
            'success' => "Ruang :modelData.nama_ruang Berhasil diubah"
        ];
        return $this->template->update($request, $rules, $message, $responseMessage);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\ruang  $ruang
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $rules = [
            'id' => ['required', 'exists:ruang,id,deleted_at,NULL']
        ];
        $message = [
            'id.exists' => 'sorry, we cannot find what are you looking for.'
        ];
        $responseMessage = [
            'success' => "Ruang :modelData.nama_ruang Berhasil dihapus"
        ];
        return $this->template->destroy($request, $rules, $message, $responseMessage);
    }
}
