<?php

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

Route::post('/server', 'API\ServerController@index');
Route::post('/server/command', 'API\ServerController@command');
Route::get('/server/log', 'API\ServerController@log');
Route::get('/server/running', 'API\ServerController@running');
