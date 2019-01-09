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

Route::group(['middleware'=>['guest']],function(){
    Route::get('/','Auth\LoginController@showLoginForm');
    Route::get('/login','Auth\LoginController@showLoginForm');
    Route::post('/login','Auth\LoginController@login')->name('login');
});

Route::group(['middleware'=>['auth']],function(){

    Route::post('/logout','Auth\LoginController@logout')->name('logout');

    Route::get('/lavado/pdf/{fecha_ini}/{fecha_fin}','LavadoController@listarPDF')->name('lavados_pdf');

    Route::get('/main', function () {
        return view('contenido/contenido');
    })->name('main');

    Route::group(['middleware'=>['Usuario']],function(){
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
        Route::get('/cliente/selectCiente', 'ClienteController@selectCiente');

        Route::get('/proveedor','ProveedorController@index');
        Route::post('/proveedor/registrar','ProveedorController@store');
        Route::put('/proveedor/actualizar','ProveedorController@update');
        Route::get('/proveedor/selectProveedor', 'ProveedorController@selectProveedor');
        
        Route::get('/lavado','LavadoController@index');
        Route::post('/lavado/registrar','LavadoController@store');
        Route::put('/lavado/actualizar','LavadoController@update');
        
        Route::get('/compra','CompraController@index');
        Route::post('/compra/registrar','CompraController@store');
        Route::put('/compra/actualizar','CompraController@update');
        Route::get('/compra/resume_compras','CompraController@exportExcel');
        
        Route::get('/venta','VentaController@index');
        Route::post('/venta/registrar','VentaController@store');
        Route::put('/venta/actualizar','VentaController@update');
        Route::get('/venta/resume_ventas','VentaController@exportExcel');
        Route::put('/venta/cancelar','VentaController@desactivar');
    });

    Route::group(['middleware'=>['Administrador']],function(){
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
        Route::get('/cliente/selectCiente', 'ClienteController@selectCiente');
        
        Route::get('/proveedor','ProveedorController@index');
        Route::post('/proveedor/registrar','ProveedorController@store');
        Route::put('/proveedor/actualizar','ProveedorController@update');
        Route::get('/proveedor/selectProveedor', 'ProveedorController@selectProveedor');
        
        Route::get('/lavado','LavadoController@index');
        Route::post('/lavado/registrar','LavadoController@store');
        Route::put('/lavado/actualizar','LavadoController@update');
        
        Route::get('/compra','CompraController@index');
        Route::post('/compra/registrar','CompraController@store');
        Route::put('/compra/actualizar','CompraController@update');
        Route::get('/compra/resume_compras','CompraController@exportExcel');
        
        Route::get('/venta','VentaController@index');
        Route::post('/venta/registrar','VentaController@store');
        Route::put('/venta/actualizar','VentaController@update');
        Route::get('/venta/resume_ventas','VentaController@exportExcel');
        Route::put('/venta/cancelar','VentaController@desactivar');
        
        Route::get('/rol','RolController@index');
        Route::get('/rol/selectRol', 'RolController@selectRol');
        
        Route::get('/user','UserController@index');
        Route::post('/user/registrar','UserController@store');
        Route::put('/user/actualizar','UserController@update');
        Route::put('/user/desactivar','UserController@desactivar');
        Route::put('/user/activar','UserController@activar');
    });
    
    
});





//Route::get('/home', 'HomeController@index')->name('home');
