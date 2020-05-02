<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Controllers\TemplateController;
use App\Peminat;
use App\Rules\table_column;
use App\Rules\unique_with;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PeminatController extends Controller
{
    protected $template = null;
    public function __construct()
    {
        $model = new Peminat();
        $this->template = new TemplateController($model, 'peminat');
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

        // validate input
        $rules = [
            'tahun_ajaran' => ['required', 'regex:/[0-9]{4,4}\/[0-9]{4,4}+$/', new unique_with('peminat,tahun_ajaran,' . $request->tahun_ajaran . ',semester,' . $request->semester . ',deleted_at,NULL')],
            'semester' => ['required', 'in:E,O'],
        ];
        $message = [
            'tahun_ajaran.regex' => "sesuaikan format :attribute dengan:  tahun/tahun"
        ];
        $responseMessage = [
            'success' => 'Peminat Tahun Ajaran :modelData.tahun_ajaran Semester :modelData.semester_detail.keterangan berhasil di tambah'
        ];
        return $this->template->store($request, $rules, $message, $responseMessage);
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
        $peminat = Peminat::find($request->id);
        try {
            $request->request->add(['peminat_id' => $peminat->peminat_id]);
        } catch (Exception $e) {
        }

        // validasi input
        $message = [
            'tahun_ajaran.unique_with' => 'Tahun Ajaran Semester already has been taken.'
        ];
        $rules = [
            'id' => ['bail', 'required', 'exists:peminat,id,deleted_at,NULL'],
            'tahun_ajaran' => ['required', 'regex:/[0-9]{4,4}\/[0-9]{4,4}+$/', new unique_with('peminat,tahun_ajaran,' . $request->tahun_ajaran . ',semester,' . $request->semester . ',deleted_at,NULL', 'id,' . $request->id, $message['tahun_ajaran.unique_with'])],
            'semester' => ['required', 'in:E,O'],
        ];
        $responseMessage = [
            'success' => 'Peminat Tahun Ajaran :modelData.tahun_ajaran Semester :modelData.semester_detail.keterangan berhasil diubah'
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
            'id' => ['required', 'exists:peminat,id,deleted_at,NULL']
        ];
        $responseMessage = [
            'success' => 'Peminat Tahun Ajaran :modelData.tahun_ajaran Semester :modelData.semester_detail.keterangan berhasil dihapus'
        ];
        return $this->template->destroy($request, $rules, [], $responseMessage);
    }
}
