<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Controllers\TemplateController;
use App\Rules\table_column;
use Illuminate\Support\Facades\Validator;
use App\Sesi as sesi;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SesiController extends Controller
{
    protected $template = null;
    public function __construct()
    {
        $model = new sesi();
        $this->template = new TemplateController($model, 'sesi');
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

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = [
            'sesi_mulai' => ['required', 'date_format:H:i:s', 'unique:sesi,sesi_mulai,NULL,id,deleted_at,NULL'],
            'sesi_selesai' => ['required', 'date_format:H:i:s', 'unique:sesi,sesi_selesai,NULL,id,deleted_at,NULL'],
        ];
        $responseMessage = [
            'success' => 'Sesi :modelData.sesi_mulai s/d :modelData.sesi_selesai Berhasil ditambahkan'
        ];
        return $this->template->store($request, $rules, [], $responseMessage);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Sesi  $sesi
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
     * @param  \App\Sesi  $sesi
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $rules = [
            'id' => ['required', 'exists:sesi,id,deleted_at,NULL'],
            'sesi_mulai' => ['required', 'date_format:H:i:s', Rule::unique('sesi', 'sesi_mulai')->ignore($request->id, 'id')],
            'sesi_selesai' => ['required', 'date_format:H:i:s', Rule::unique('sesi', 'sesi_selesai')->ignore($request->id, 'id')],
        ];
        $message = [
            'id.exists' => 'sorry, we cannot find what are you looking for.'
        ];
        $responseMessage = [
            'success' =>  'Sesi :modelData.sesi_mulai s/d :modelData.sesi_selesai Berhasil diubah'
        ];
        return $this->template->update($request, $rules, $message, $responseMessage);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Sesi  $sesi
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $rules = [
            'id' => ['required', 'exists:sesi,id,deleted_at,NULL']
        ];
        $message = [
            'id.exists' => 'sorry, we cannot find what are you looking for.'
        ];

        $responseMessage = [
            'success' =>  'Sesi :modelData.sesi_mulai s/d :modelData.sesi_selesai Berhasil dihapus'
        ];
        return $this->template->destroy($request, $rules, $message, $responseMessage);
    }
}
