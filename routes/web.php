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

Route::get('/', function () {
    return view('contenido/contenido');
});

Route::get('/servicio','ServicioController@index');
Route::post('/servicio/registrar','ServicioController@store');
Route::put('/servicio/actualizar','ServicioController@update');
Route::delete('/servicio/eliminar','ServicioController@destroy');
Route::get('/servicio/selectServicio', 'ServicioController@selectServicio');
Route::get('/servicio/selectPrecioServicio', 'ServicioController@selectPrecioServicio');

Route::get('/cliente','ClienteController@index');
Route::post('/cliente/registrar','ClienteController@store');
Route::put('/cliente/actualizar','ClienteController@update');
Route::delete('/cliente/eliminar','ClienteController@destroy');

Route::get('/proveedor','ProveedorController@index');
Route::post('/proveedor/registrar','ProveedorController@store');
Route::put('/proveedor/actualizar','ProveedorController@update');

Route::get('/lavado','LavadoController@index');
Route::post('/lavado/registrar','LavadoController@store');
Route::put('/lavado/actualizar','LavadoController@update');

Route::get('/rol','RolController@index');
Route::get('/rol/selectRol', 'RolController@selectRol');

Route::get('/user','UserController@index');
Route::post('/user/registrar','UserController@store');
Route::put('/user/actualizar','UserController@update');
Route::put('/user/desactivar','UserController@desactivar');
Route::put('/user/activar','UserController@activar');
