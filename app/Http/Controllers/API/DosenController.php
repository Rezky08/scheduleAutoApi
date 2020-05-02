<?php


namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Dosen as dosen;
use App\Http\Controllers\TemplateController;
use App\Rules\table_column;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class DosenController extends Controller
{
    protected $template = null;
    public function __construct()
    {
        $model = new dosen();
        $this->template = new TemplateController($model, 'dosen');
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
            'kode_dosen' => ['required', 'unique:dosen,kode_dosen,NULL,id,deleted_at,NULL'],
            'nama_dosen' => ['required'],
        ];
        $responseMessage = [
            'success' => 'Dosen :modelData.nama_dosen ( :modelData.kode_dosen ) berhasil ditambahkan'
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
        return $this->template->search($request);
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
            'id' => ['required', 'exists:dosen,id,deleted_at,NULL'],
            'kode_dosen' => ['required', Rule::unique('dosen', 'kode_dosen')->ignore($request->id, 'id')],
            'nama_dosen' => ['required', 'max:100'],
        ];
        $message = [
            'kode_dosen.exists' => 'sorry, we cannot find what are you looking for.'
        ];
        $responseMessage = [
            'success' => "Dosen :modelData.nama_dosen ( :modelData.kode_dosen ) berhasil diubah"
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
            'id' => ['required', 'exists:dosen,id,deleted_at,NULL']
        ];
        $message = [
            'id.exists' => 'sorry, we cannot find what are you looking for.'
        ];
        $responseMessage = [
            'success' => "Dosen :modelData.nama_dosen ( :modelData.kode_dosen ) berhasil dihapus"
        ];
        return $this->template->destroy($request, $rules, $message, $responseMessage);
    }
}
