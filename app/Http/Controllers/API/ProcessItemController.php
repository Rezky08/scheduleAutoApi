<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\ProcessItem;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProcessItemController extends Controller
{
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
        try {
            $process_item = ProcessItem::all();
            $response = [
                'status' => 200,
                'data' => $process_item
            ];
            return response()->json($response, $response['status']);
        } catch (Exception $e) {
            $response = [
                'status' => 500,
                'message' => "Internal Server Error"
            ];
            if (env('APP_DEBUG') == true) {
                $response['message'] = $e->getMessage();
            }
            return response()->json($response, $response['status']);
        }
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
            'process_name' => ['required', 'unique:process_item,process_name']
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $response = [
                'status' => 400,
                'message' => $validator->errors()
            ];
            return response()->json($response, $response['status']);
        }

        $insertToDB = [
            'process_name' => $request->process_name,
            'created_at' => date('Y-m-d H:i:s', time()),
            'updated_at' => date('Y-m-d H:i:s', time()),
        ];
        try {

            ProcessItem::insert($insertToDB);
            $response = [
                'status' => 200,
                'message' => "Process Item Has Been Added."
            ];

            return response()->json($response, $response['status']);
        } catch (Exception $e) {
            $response = [
                'status' => 500,
                'message' => "Internal Server Error"
            ];
            if (env('APP_DEBUG') == true) {
                $response['message'] = $e->getMessage();
            }
            return response()->json($response, $response['status']);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
