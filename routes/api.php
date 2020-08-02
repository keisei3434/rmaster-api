<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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
/*
Route::group(['middleware' => ['api']], function () {
    Route::get('user', 'UserController@show');
});
*/
Route::apiResource('/user', 'UserController', ['except' => ['create', 'edit']]);
Route::apiResource('/match', 'MatchController', ['except' => ['create', 'edit']]);
Route::apiResource('/user', 'PlaceController', ['except' => ['create', 'edit']]);
Route::apiResource('/match', 'PracticeController', ['except' => ['create', 'edit']]);
Route::apiResource('/user', 'ResultController', ['except' => ['create', 'edit']]);