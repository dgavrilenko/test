<?php

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

Auth::routes();

Route::resource('/', 'MainController');
Route::resource('/catalog', 'CatalogController');
Route::resource('/basket', 'BasketController');
Route::resource('/orders', 'OrderController');

Route::delete('/api/basket/clear', 'Api\BasketController@clear');
Route::resource('/api/basket', 'Api\BasketController');

Route::resource('/api/products', 'Api\ProductsController');
Route::resource('/api/allergens', 'Api\AllergensController');

Route::get('/api/request/export/{id}', 'Api\RequestController@export');
Route::resource('/api/request', 'Api\RequestController');

