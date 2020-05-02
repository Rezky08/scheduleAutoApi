<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Controllers\TemplateController;
use App\ProcessLog;
use Illuminate\Http\Request;

class ProcessLogController extends Controller
{
    protected $template = null;
    protected $model = null;
    public function __construct()
    {
        $this->model = new ProcessLog();
        $this->template = new TemplateController($this->model, 'process_log');
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
            'process_item_id' => ['required', 'exists:process_item,id,deleted_at,NULL'],
            'item_key' => ['required', 'numeric'],
        ];
        return $this->template->store($request, $rules);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\ProcessLog  $processLog
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
     * @param  \App\ProcessLog  $processLog
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $rules = [
            'id' => ['required', 'exists:process_log,id,deleted_at,NULL'],
            'process_item_id' => ['required', 'exists:process_item,id,deleted_at,NULL'],
            'item_key' => ['required', 'numeric'],
        ];
        return $this->template->update($request, $rules);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\ProcessLog  $processLog
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $rules = [
            'id' => ['required', 'exists:process_log,id,deleted_at,NULL']
        ];
        return $this->template->delete($request, $rules);
    }
}
