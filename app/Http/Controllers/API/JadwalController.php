<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Controllers\TemplateController;
use App\Jadwal;
use App\Rules\unique_with;
use Exception;
use Illuminate\Http\Request;

class JadwalController extends Controller
{

    protected $template = null;
    protected $model = null;
    public function __construct()
    {
        $this->model = new Jadwal();
        $this->template = new TemplateController($this->model, 'jadwal');
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

        // validate input
        $rules = [
            'tahun_ajaran' => ['required', 'regex:/[0-9]{4,4}\/[0-9]{4,4}+$/', new unique_with('jadwal,tahun_ajaran,' . $request->tahun_ajaran . ',semester,' . $request->semester . ',deleted_at,NULL')],
            'semester' => ['required', 'in:E,O'],
        ];
        $message = [
            'tahun_ajaran.regex' => "sesuaikan format :attribute dengan:  tahun/tahun"
        ];
        $responseMessage = [
            'success' => 'Jadwal Tahun Ajaran :modelData.tahun_ajaran Semester :modelData.semester_detail.keterangan berhasil di tambah'
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
        $jadwal = $this->model->find($request->id);
        try {
            $request->request->add(['jadwal_id' => $jadwal->jadwal_id]);
        } catch (Exception $e) {
        }

        // validasi input
        $message = [
            'tahun_ajaran.unique_with' => 'Tahun Ajaran Semester already has been taken.'
        ];
        $rules = [
            'id' => ['bail', 'required', 'exists:jadwal,id,deleted_at,NULL'],
            'tahun_ajaran' => ['required', 'regex:/[0-9]{4,4}\/[0-9]{4,4}+$/', new unique_with('jadwal,tahun_ajaran,' . $request->tahun_ajaran . ',semester,' . $request->semester . ',deleted_at,NULL', 'id,' . $request->id, $message['tahun_ajaran.unique_with'])],
            'semester' => ['required', 'in:E,O'],
        ];
        $responseMessage = [
            'success' => 'Jadwal Tahun Ajaran :modelData.tahun_ajaran Semester :modelData.semester_detail.keterangan berhasil diubah'
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
            'id' => ['required', 'exists:jadwal,id,deleted_at,NULL']
        ];
        $responseMessage = [
            'success' => 'Jadwal Tahun Ajaran :modelData.tahun_ajaran Semester :modelData.semester_detail.keterangan berhasil dihapus'
        ];
        return $this->template->destroy($request, $rules, [], $responseMessage);
    }
}
