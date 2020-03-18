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

Route::get('/anchors', 'AnchorController@index')->name('anchors');

Route::get('/anchors/create', 'AnchorController@create')->name('anchors.create');

Route::post('/anchors', 'AnchorController@store');

Route::get('/anchors/getrank/{q}', 'AnchorController@result');

Route::get('/anchors/getanchor/{keyword}/{rank}', 'AnchorController@detail');

