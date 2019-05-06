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

Auth::routes();

Route::get('/', function() {
    return redirect()->route('login');
});

Route::middleware(['auth'])->prefix('admin')->group(function () {
    Route::get('/', 'RouterController@show')->name('admin.index');
    Route::get('/{view}', 'RouterController@show')->where('view', '.*');
});
