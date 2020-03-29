<?php

namespace App\Http\Controllers;

use App\Rules\table_column;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class TemplateController extends Controller
{
    protected $model = null;
    protected $table_name = '';
    protected $responseMessage = [];

    public function __construct($model = null, $table_name = '', $responseMessage = [])
    {
        $this->model = $model;
        $this->table_name = $table_name;
        if (isset($responseMessage['success'])) {
            $this->responseMessage['success'] = $responseMessage['success'];
        } else {
            $this->responseMessage['success'] = $this->table_name . " Success!";
        }

        if (isset($responseMessage['error'])) {
            $this->responseMessage['error'] = $responseMessage['error'];
        } else {
            $this->responseMessage['error'] = "Internal Server Error";
        }
    }

    public function setResponseMessage($responseMessage = [])
    {
        if (isset($responseMessage['success'])) {
            $this->responseMessage['success'] = $responseMessage['success'];
        } else {
            $this->responseMessage['success'] = $this->table_name . " Success!";
        }

        if (isset($responseMessage['error'])) {
            $this->responseMessage['error'] = $responseMessage['error'];
        } else {
            $this->responseMessage['error'] = "Internal Server Error";
        }
        return $this;
    }

    /*
    format 'model.variablename'
     */
    public function __translate($variable = '', $modelData = null)
    {
        preg_match_all("/\:\S{0,}/", $variable, $variable_array);
        $variable_array = collect($variable_array[0]);
        $variable_array = $variable_array->map(function ($item, $key) use ($modelData, &$variable) {
            $item = preg_replace('/\:/', '', $item);
            $item = explode('.', $item);
            $variabel_name = $item[0];
            $variable_item = $item[1];
            $variable = preg_replace("/\:\S{0,}/", $$variabel_name[$variable_item], $variable, 1);
        });

        return $variable;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $responseMessage = [])
    {
        $this->setResponseMessage($responseMessage);


        // check apakah ada request
        if (count($request->all()) > 0) {
            return $this->show($request);
        }

        try {
            $modelData = $this->model->all();
            $response = [
                'status' => 200,
                'data' => $modelData->toArray()
            ];
            return response()->json($response, $response['status']);
        } catch (Exception $e) {
            $response = [
                'status' => 500,
                'message' => $this->responseMessage['error']
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
    public function store(Request $request, $rules = [], $validateMessage = [], $responseMessage = [])
    {
        $this->setResponseMessage($responseMessage);

        $validator = Validator::make($request->all(), $rules, $validateMessage);
        if ($validator->fails()) {
            $response = [
                'status' => 400,
                'message' => $validator->errors()
            ];
            return response()->json($response, $response['status']);
        }
        // key accepted
        $accepted_key = collect($rules)->except('id')->keys()->toArray();
        $insertToDB = collect($request->all())->only($accepted_key)->toArray();
        $insertToDB += [
            'created_at' => date("Y-m-d H:i:s", time()),
            'updated_at' => date("Y-m-d H:i:s", time())
        ];

        try {
            $id = $this->model->insertGetId($insertToDB);
            $modelData = $this->model->find($id);
            $response = [
                'status' => 200,
                'message' => $this->__translate($this->responseMessage['success'], $modelData)
            ];
            return response()->json($response, $response['status']);
        } catch (Exception $e) {
            $response = [
                'status' => 500,
                'message' => $this->responseMessage['error']
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
    public function show(Request $request, $responseMessage = [])
    {
        $this->setResponseMessage($responseMessage);

        $table_column = collect($request->all())->keys()->toArray();
        $rules = [
            'column' => ['required', new table_column($this->table_name)]
        ];
        $validator = Validator::make($request->all() + ['column' => $table_column], $rules);
        if ($validator->fails()) {
            $response = [
                'status' => 400,
                'message' => $validator->errors()
            ];
            return response()->json($response, $response['status']);
        }

        $whereCond = collect($request->all());
        $whereCond = $whereCond->map(function ($item) {
            return $item;
        });

        try {
            $modelData = $this->model->where($request->all())->get();
            $response = [
                'status' => 200,
                'data' => $modelData
            ];
            return response()->json($response, $response['status']);
        } catch (Exception $e) {
            $response = [
                'status' => 500,
                'message' => $this->responseMessage['error']
            ];
            if (env('APP_DEBUG') == true) {
                $response['message'] = $e->getMessage();
            }
            return response()->json($response, $response['status']);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $rules = [], $validateMessage = [], $responseMessage = [])
    {
        $this->setResponseMessage($responseMessage);

        $validator = Validator::make($request->all(), $rules, $validateMessage);
        if ($validator->fails()) {
            $response = [
                'status' => 400,
                'message' => $validator->errors()
            ];
            return response()->json($response, $response['status']);
        }

        // key accepted
        $accepted_key = collect($rules)->except('id')->keys()->toArray();
        $update = collect($request->all())->only($accepted_key);

        try {
            $modelData = $this->model->find($request->id);
            $update->map(function ($item, $key) use ($modelData) {
                $modelData[$key] = $item;
            });
            $modelData->save();

            $response = [
                'status' => 200,
                'message' => $this->__translate($this->responseMessage['success'], $modelData)
            ];

            if (!$modelData->getChanges()) {
                $response['message'] = "Tidak ada perubahan";
            }

            return response()->json($response, $response['status']);
        } catch (Exception $e) {
            $response = [
                'status' => 500,
                'message' => $this->responseMessage['error']
            ];
            if (env('APP_DEBUG') == true) {
                $response['message'] = $e->getMessage();
            }
            return response()->json($response, $response['status']);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $rules = [], $validateMessage = [], $responseMessage = [])
    {
        $this->setResponseMessage($responseMessage);

        $validator = Validator::make($request->all(), $rules, $validateMessage);
        if ($validator->fails()) {
            $response = [
                'status' => 400,
                'message' => $validator->errors()
            ];
            return response()->json($response, $response['status']);
        }

        try {
            $modelData = $this->model->find($request->id);
            $modelData->delete();

            $response = [
                'status' => 200,
                'message' => $this->__translate($this->responseMessage['success'], $modelData)
            ];
            return response()->json($response, $response['status']);
        } catch (Exception $e) {
            $response = [
                'status' => 500,
                'message' => $this->responseMessage['error']
            ];
            if (env('APP_DEBUG') == true) {
                $response['message'] = $e->getMessage();
            }
            return response()->json($response, $response['status']);
        }
    }
}
