<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Hari as hari;
use App\Http\Controllers\TemplateController;
use App\Rules\table_column;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class HariController extends Controller
{
    protected $template = null;
    public function __construct()
    {
        $model = new hari();
        $this->template = new TemplateController($model, 'hari');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // check apakah ada request
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
            'nama_hari' => ['required', 'unique:hari,nama_hari,NULL,id,deleted_at,NULL'],
        ];
        $responseMessage = [
            'success' => "Hari :modelData.nama_hari Berhasil ditambahkan"
        ];
        return $this->template->store($request, $rules, [], $responseMessage);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Hari  $hari
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
     * @param  \App\Hari  $hari
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $rules = [
            'id' => ['required', 'exists:hari,id,deleted_at,NULL'],
            'nama_hari' => ['required', Rule::unique('hari', 'nama_hari')->ignore($request->id, 'id')]
        ];
        $message = [
            'id.exists' => 'sorry, we cannot find what are you looking for.'
        ];
        $responseMessage = [
            'success' =>  "Hari :modelData.nama_hari Berhasil diubah"
        ];
        return $this->template->update($request, $rules, $message, $responseMessage);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Hari  $hari
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $rules = [
            'id' => ['required', 'exists:hari,id,deleted_at,NULL']
        ];
        $message = [
            'id.exists' => 'sorry, we cannot find what are you looking for.'
        ];
        $responseMessage = [
            'success' =>  "Hari :modelData.nama_hari Berhasil dihapus"
        ];
        return $this->template->destroy($request, $rules, $message, $responseMessage);
    }
}
