<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Controllers\TemplateController;
use App\ProcessItem;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ProcessItemController extends Controller
{
    protected $template = null;
    protected $model = null;
    public function __construct()
    {
        $this->model = new ProcessItem();
        $this->template = new TemplateController($this->model, 'process_item');
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
            'process_name' => ['required', 'unique:process_item,process_name,NULL,id,deleted_at,NULL'],
            'description' => ['sometimes', 'required']
        ];
        $responseMessage = [
            'success' => "Process :modelData.process_name has been added"
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
            'id' => ['required', 'exists:process_item,id,deleted_at,NULL'],
            'process_name' => ['required', Rule::unique('process_item', 'process_name')->ignore($request->id, 'id')],
            'description' => ['sometimes', 'required']
        ];
        $responseMessage = [
            'success' => "Process :modelData.process_name has been updated"
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
            'id' => ['required', 'exists:process_item,id,deleted_at,NULL'],
        ];
        $responseMessage = [
            'success' => "Process :modelData.process_name has been deleted"
        ];
        return $this->template->destroy($request, $rules, [], $responseMessage);
    }
}
