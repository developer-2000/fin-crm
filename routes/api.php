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

Route::match(['get', 'post'], '/get-status-order2', [
    'as'   => 'get-status-order2',
    'uses' => 'OrderController@getStatusOrder2'
]);

Route::match(['get', 'post'], '/get-status-order4', [
    'as'   => 'get-status-order4',
    'uses' => 'OrderApiController@getStatusOrder4'
]);

Route::match(['get', 'post'], '/set-order', [
    'as'   => 'set-order',
    'uses' => 'OrderApiController@setOrder'
]);

Route::match(['get', 'post'], '/set-exist-order', [
    'as'   => 'set-exist-order',
    'uses' => 'OrderApiController@setExistOrder'
]);

Route::match(['get', 'post'], '/get-result-order', [
    'as'    => 'get-result-order-api',
    'uses'  => 'OrderApiController@getResultOrder'
]);

Route::post('/ninjaxpress/webhooks', [
    'as'    => 'ninjaxpress-webhooks',
    'uses'  => 'Api\NinjaxpressApiController@track'
]);