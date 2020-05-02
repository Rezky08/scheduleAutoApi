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
Route::get('/matkul/search', 'API\MatakuliahController@show');
Route::put('/matkul', 'API\MatakuliahController@update');
Route::delete('/matkul', 'API\MatakuliahController@destroy');

Route::post('/program_studi', 'API\ProgramStudiController@store');
Route::get('/program_studi', 'API\ProgramStudiController@index');
Route::get('/program_studi/search', 'API\ProgramStudiController@show');
Route::put('/program_studi', 'API\ProgramStudiController@update');
Route::delete('/program_studi', 'API\ProgramStudiController@destroy');

Route::post('/ruang', 'API\RuangController@store');
Route::get('/ruang', 'API\RuangController@index');
Route::get('/ruang/search', 'API\RuangController@show');
Route::put('/ruang', 'API\RuangController@update');
Route::delete('/ruang', 'API\RuangController@destroy');

Route::post('/hari', 'API\HariController@store');
Route::get('/hari', 'API\HariController@index');
Route::get('/hari/search', 'API\HariController@show');
Route::put('/hari', 'API\HariController@update');
Route::delete('/hari', 'API\HariController@destroy');

Route::post('/sesi', 'API\SesiController@store');
Route::get('/sesi', 'API\SesiController@index');
Route::get('/sesi/search', 'API\SesiController@show');
Route::put('/sesi', 'API\SesiController@update');
Route::delete('/sesi', 'API\SesiController@destroy');

Route::get('/peminat', 'API\PeminatController@index');
Route::get('/peminat/search', 'API\PeminatController@show');
Route::put('/peminat', 'API\PeminatController@update');
Route::post('/peminat', 'API\PeminatController@store');
Route::delete('/peminat', 'API\PeminatController@destroy');


Route::get('/peminat/detail', 'API\PeminatDetailController@index');
Route::get('/peminat/detail/search', 'API\PeminatDetailController@show');
Route::post('/peminat/detail', 'API\PeminatDetailController@store');
Route::put('/peminat/detail', 'API\PeminatDetailController@update');
Route::delete('/peminat/detail', 'API\PeminatDetailController@destroy');



Route::get('/dosen', 'API\DosenController@index');
Route::get('/dosen/search', 'API\DosenController@show');
Route::post('/dosen', 'API\DosenController@store');
Route::put('/dosen', 'API\DosenController@update');
Route::delete('/dosen', 'API\DosenController@destroy');

Route::get('/kelompok_dosen', 'API\KelompokDosenController@index');
Route::get('/kelompok_dosen/search', 'API\KelompokDosenController@show');
Route::post('/kelompok_dosen', 'API\KelompokDosenController@store');
Route::put('/kelompok_dosen', 'API\KelompokDosenController@update');
Route::delete('/kelompok_dosen', 'API\KelompokDosenController@destroy');

Route::get('/kelompok_dosen/detail', 'API\KelompokDosenDetailController@index');
Route::get('/kelompok_dosen/detail/search', 'API\KelompokDosenDetailController@show');
Route::post('/kelompok_dosen/detail', 'API\KelompokDosenDetailController@store');
Route::put('/kelompok_dosen/detail', 'API\KelompokDosenDetailController@update');
Route::delete('/kelompok_dosen/detail', 'API\KelompokDosenDetailController@destroy');

Route::get('/dosen_matkul', 'API\DosenMatkulController@index');
Route::get('/dosen_matkul/search', 'API\DosenMatkulController@show');
Route::post('/dosen_matkul', 'API\DosenMatkulController@store');
Route::put('/dosen_matkul', 'API\DosenMatkulController@update');
Route::delete('/dosen_matkul', 'API\DosenMatkulController@destroy');


Route::get('/python', 'API\PythonEngineController@index');
Route::post('/python', 'API\PythonEngineController@store');

Route::post('/process/item', 'API\ProcessItemController@store');
Route::get('/process/item', 'API\ProcessItemController@index');
Route::get('/process/item/search', 'API\ProcessItemController@show');
Route::put('/process/item', 'API\ProcessItemController@update');
Route::delete('/process/item', 'API\ProcessItemController@destroy');



Route::get('/jadwal', 'API\JadwalController@index');
Route::get('/jadwal/search', 'API\JadwalController@show');
Route::post('/jadwal', 'API\JadwalController@store');
Route::put('/jadwal', 'API\JadwalController@update');
Route::delete('/jadwal', 'API\JadwalController@destroy');

Route::get('/jadwal/detail', 'API\JadwalDetailController@index');
Route::get('/jadwal/detail/search', 'API\JadwalDetailController@show');
Route::post('/jadwal/detail', 'API\JadwalDetailController@store');
Route::put('/jadwal/detail', 'API\JadwalDetailController@update');
Route::delete('/jadwal/detail', 'API\JadwalDetailController@destroy');

Route::post('/python/kelompok_dosen', 'API\PythonEngineController@storeKelompokDosen');
Route::post('/python/jadwal', 'API\PythonEngineController@storeJadwal');
