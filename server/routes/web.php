<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/camera', 'CameraController@index');
Route::post('/camera', 'CameraController@store');
Route::get('/camera/{id}', 'CameraController@camera');
Route::post('/run', 'CameraController@runService');
Route::post('/generate', 'CameraController@generateImage');

Route::get('/trend', 'CountingController@trend');
Route::get('/trend/{id}', 'CountingController@trend');
Route::get('/weekly/trend', 'CountingController@trend_week');

Route::get('/pie', 'CountingController@pie');
Route::get('/pie/{id}', 'CountingController@pie');

Route::get('/heatmap', 'CountingController@heatmap');

Route::get('/realtime/count/{id}', 'CountingController@liveCount');