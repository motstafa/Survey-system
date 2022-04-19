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


    Route::post('login', [App\Http\Controllers\usersController::class, 'login']);
    Route::post('logout', [App\Http\Controllers\usersController::class, 'logout']);
    Route::post('refresh', [App\Http\Controllers\usersController::class, 'refresh']);
    Route::post('me', [App\Http\Controllers\usersController::class, 'me']);
    Route::post('register', [App\Http\Controllers\usersController::class, 'register']);


Route::post('/survey','surveyController@store');
Route::get('/survey/{id_user}','surveyController@getSurvey');

