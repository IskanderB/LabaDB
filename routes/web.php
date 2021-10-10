<?php

use Illuminate\Support\Facades\Route;


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

Route::get('/testbl', 'App\Http\Controllers\TestBL\PageController@index')->name('testbl');

Route::name('testbl.')->group(function () {
    Route::post('/testbl/makedir', 'App\Http\Controllers\TestBL\BLController@makedir')->name('makedir');
    Route::post('/testbl/makefiles', 'App\Http\Controllers\TestBL\BLController@makefiles')->name('makefiles');
    Route::post('/testbl/rmdir', 'App\Http\Controllers\TestBL\BLController@rmdir')->name('rmdir');
    Route::post('/testbl/cleardir', 'App\Http\Controllers\TestBL\BLController@cleardir')->name('cleardir');
});
