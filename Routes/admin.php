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
*/

// 文章分类
Route::get('article_cat/list', 'ArticleCatController@list');
Route::get('article_cat/ajaxList', 'ArticleCatController@ajaxList');
Route::post('article_cat/del', 'ArticleCatController@del');
Route::any('article_cat/edit', 'ArticleCatController@edit');
Route::post('article_cat/saveStatus', 'ArticleCatController@saveStatus');
Route::post('article_cat/saveRecommend', 'ArticleCatController@saveRecommend');

// 文章
Route::get('article/list', 'ArticleController@list');
Route::get('article/ajaxList', 'ArticleController@ajaxList');
Route::post('article/del', 'ArticleController@del');
Route::any('article/edit', 'ArticleController@edit');
Route::any('article/batchEdit', 'ArticleController@batchEdit');
Route::post('article/saveStatus', 'ArticleController@saveStatus');
Route::post('article/saveField', 'ArticleController@saveField');
Route::post('article/xmSelect', 'ArticleController@xmSelect');

// 回收站
Route::get('article_recycle_bin/list', 'ArticleRecycleBinController@list');
Route::get('article_recycle_bin/ajaxList', 'ArticleRecycleBinController@ajaxList');
Route::post('article_recycle_bin/del', 'ArticleRecycleBinController@del');
Route::any('article_recycle_bin/details', 'ArticleRecycleBinController@details');
Route::post('article_recycle_bin/recovery', 'ArticleRecycleBinController@recovery');

// 属性
Route::get('article_attr/list', 'ArticleAttrController@list');
Route::get('article_attr/ajaxList', 'ArticleAttrController@ajaxList');
Route::post('article_attr/del', 'ArticleAttrController@del');
Route::any('article_attr/edit', 'ArticleAttrController@edit');

// 标签
Route::get('article_tag/list', 'ArticleTagController@list');
Route::get('article_tag/ajaxList', 'ArticleTagController@ajaxList');
Route::post('article_tag/del', 'ArticleTagController@del');
Route::any('article_tag/edit', 'ArticleTagController@edit');
