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

/*Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});*/
Route::get('index', 'FrontController@index');
Route::get('contacts', 'FrontController@contacts');
Route::get('getProduct/{slug}', 'FrontController@getProduct');
Route::post('getPass', 'SendController@getPass');

Route::group(['prefix' => 'admin', 'namespace' => 'Admin', 'middleware' => ['auth:api']], function (){
    Route::apiResource('description', 'DescriptionController');
    Route::apiResource('detail', 'DetailController');
    Route::apiResource('file', 'FileController');
    Route::apiResource('info', 'InfoController');
    Route::delete('product/delProdDetRelation/{prId}/{detId}', 'ProductController@delProdDetRelation');
    Route::delete('product/delProdPic/{picId}', 'ProductController@delProdPic');
    Route::post('product/updProdPic', 'ProductController@updProdPic');
    Route::apiResource('product', 'ProductController');
    Route::apiResource('service', 'ServiceController');
    Route::apiResource('partner', 'PartnerController');
    Route::apiResource('contact', 'ContactController');
    Route::apiResource('slide', 'SlideController');
    Route::apiResource('user', 'UserController');
    Route::post('uploadEditorImage', 'UploadController@uploadEditorImage');
});

Route::group(['prefix' => 'auth'], function (){
    Route::post('register', 'Auth\RegisterController@action');
    Route::post('login', 'Auth\LoginController@action');
    Route::post('refresh', 'Auth\AuthController@refresh');
    Route::post('logout', 'Auth\LogoutController@action');
    Route::get('me', 'Auth\MeController@action');
});
