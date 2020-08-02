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
Route::apiResource('/place', 'PlaceController', ['except' => ['create', 'edit']]);
Route::apiResource('/practice', 'PracticeController', ['except' => ['create', 'edit']]);
Route::apiResource('/result', 'ResultController', ['except' => ['create', 'edit']]);