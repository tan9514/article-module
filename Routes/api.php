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

// 接口
Route::get('article/getCatList', 'ArticleController@getCatList');
Route::get('article/getArticlePageList', 'ArticleController@getArticlePageList');
Route::get('article/getDetails', 'ArticleController@getDetails');
Route::get('article/ranking', 'ArticleController@ranking');

// 验证登录才能使用的接口
Route::middleware("auth.admin:api")->group(function () {
    Route::post('article/agree', 'ArticleController@agree');
    Route::get('article/getAgreePageList', 'ArticleController@getAgreePageList');
    Route::post('article/favorite', 'ArticleController@favorite');
    Route::get('article/getFavoritePageList', 'ArticleController@getFavoritePageList');
});