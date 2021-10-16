<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DB\CreateController;
use App\Http\Controllers\DB\RemoveController;

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

//Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//    return $request->user();
//});

Route::prefix('/' . env('API_VERSION'))->group(function () {
    Route::prefix('/db')->group(function () {
        Route::post(
            '/create',
            [CreateController::class, 'create']
        )->name('create');
        Route::delete(
            '/remove',
            [RemoveController::class, 'remove']
        )->name('remove');
    });
});
