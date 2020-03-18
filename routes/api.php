<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('/matkul', 'API\MatakuliahController@store');
Route::get('/matkul', 'API\MatakuliahController@index');
Route::put('/matkul', 'API\MatakuliahController@update');
Route::delete('/matkul', 'API\MatakuliahController@destroy');

Route::post('/program_studi', 'API\ProgramStudiController@store');
Route::get('/program_studi', 'API\ProgramStudiController@index');
Route::put('/program_studi', 'API\ProgramStudiController@update');
Route::delete('/program_studi', 'API\ProgramStudiController@destroy');

Route::post('/ruang', 'API\ruangController@store');
Route::get('/ruang', 'API\ruangController@index');
Route::put('/ruang', 'API\ruangController@update');
Route::delete('/ruang', 'API\ruangController@destroy');

Route::post('/hari', 'API\HariController@store');
Route::get('/hari', 'API\HariController@index');
Route::put('/hari', 'API\HariController@update');
Route::delete('/hari', 'API\HariController@destroy');

Route::post('/jam', 'API\JamController@store');
Route::get('/jam', 'API\JamController@index');
Route::put('/jam', 'API\JamController@update');
Route::delete('/jam', 'API\JamController@destroy');
