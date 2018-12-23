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
Route::group([
    'middleware' => 'auth:api'
], function() {
    Route::get('/users/me', 'UserControllerAPI@myProfile');
    Route::put('/users/me', 'UserControllerAPI@save');
    Route::post('logout', 'LoginControllerAPI@logout');
    Route::post('/account/create', 'UserControllerAPI@create');
    Route::post('users/me/photo', 'FileController@store');
});
Route::get('users', 'UserControllerAPI@index');
Route::middleware('auth:api')->get('users/me', 'UserControllerAPI@myProfile');

Route::get('menu', 'ItemControllerAPI@index');

Route::get('orders', 'OrderControllerAPI@index')/* ->middleware(['auth:api', 'scopes:manage-order']) */;
Route::put('orders/{id}', 'OrderControllerAPI@update')/* ->middleware(['auth:api', 'scopes:manage-order']) */;

Route::post('login', 'LoginControllerAPI@login');
Route::post('/account/confirm', 'UserControllerAPI@confirm');
Route::group([
    'namespace' => 'Auth',
    'middleware' => 'api',
    'prefix' => 'password'
], function () {
    Route::post('create', 'PasswordResetController@create');
    Route::get('find/{token}', 'PasswordResetController@find');
    Route::post('reset', 'PasswordResetController@reset');
});