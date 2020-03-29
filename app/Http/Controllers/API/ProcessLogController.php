<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\ProcessLog;
use Illuminate\Http\Request;

class ProcessLogController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\ProcessLog  $processLog
     * @return \Illuminate\Http\Response
     */
    public function show(ProcessLog $processLog)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\ProcessLog  $processLog
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ProcessLog $processLog)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\ProcessLog  $processLog
     * @return \Illuminate\Http\Response
     */
    public function destroy(ProcessLog $processLog)
    {
        //
    }
}
