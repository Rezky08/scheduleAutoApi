<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\AlgenResultLog;
use App\Http\Controllers\TemplateController;
use Illuminate\Http\Request;

class AlgenResultLogController extends Controller
{
    protected $template = null;
    public function __construct()
    {
        $model = new AlgenResultLog();
        $this->template = new TemplateController($model, 'algen_result');
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
            'process_log_id' => ['required', 'exists:process_log,id,deleted_at,NULL'],
            'result_key' => ['required'],
            'fit_score' => ['required']
        ];
        return $this->template->store($request, $rules);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\AlgenResultLog  $algenResultLog
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
     * @param  \App\AlgenResultLog  $algenResultLog
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $rules = [
            'id' => ['required', 'exists:algen_result,id,deleted_at,NULL'],
            'process_log_id' => ['required', 'exists:process_log,id,deleted_at,NULL'],
            'result_key' => ['required'],
            'fit_score' => ['required']
        ];
        return $this->template->store($request, $rules);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\AlgenResultLog  $algenResultLog
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $rules = [
            'id' => ['required', 'exists:algen_result,id,deleted_at,NULL'],
        ];
        return $this->template->store($request, $rules);
    }
}
