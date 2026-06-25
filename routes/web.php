<?php

use App\Http\Controllers\TonerController;
use App\Http\Controllers\PermissionController;
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

/* Auth */

Route::get('/login', function () {
    return view('auth.login');
});

Route::get('index/{locale}', [App\Http\Controllers\HomeController::class, 'lang']);

Auth::routes();

   // Route::post('login', 'Auth\LoginController@login')->name('login');
   // Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
    Route::post('register', 'Auth\RegisterController@register')->name('register');
   // Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
   // Route::post('password/reset', 'Auth\ResetPasswordController@reset')->name('password.update');

Auth::routes(['verify' => true]);

Route::group(['prefix' => 'error'], function(){
    Route::get('404', function () { return view('error.404'); });
    Route::get('500', function () { return view('error.500'); });
});

Route::middleware(['auth'])->group(function () {

    Route::resource('tokens', \App\Http\Controllers\CwtokenController::class);
    Route::controller(\App\Http\Controllers\CwtokenController::class)->group(function () {
        Route::match(['get','post'],'reporte/tokens', 'reportetokens')->name('reportetokens');
        Route::post('token/update', 'tokenupdate')->name('tokenupdate');
    });

    Route::get('/buscarproducto/{codprod}/{comercial}', [\App\Http\Controllers\SaprodController::class, 'buscarproductoget'])->name('buscarproductoget');

    Route::resource('transferencias', \App\Http\Controllers\CwtransferenciasController::class);
    Route::controller(\App\Http\Controllers\CwtransferenciasController::class)->group(function () {

        Route::match(['get','post'],'reporte/transferencias', 'reportetransferencias')->name('reportetransferencias');
        Route::post('transferencias/json/{busquedatransf}/{status}/{fechas}', 'json');
        Route::get('transferencias/status/{status}', 'filtrarstatus');
        Route::post('transferencias/pendienteAgain', 'pendienteAgain');
        Route::post('transferencias/verificar', 'verificar');
        Route::match(['get','post'],'transferencia/informacion', 'informacion');
    });

    Route::get('/verpermisos/{id?}', [PermissionController::class, 'showForm'])->name('permissions.assign');
    Route::post('/verpermisos', [PermissionController::class, 'assign']);
    // Crear nuevo permiso
    Route::post('/create/permissions', [PermissionController::class, 'create'])->name('permissions.create');
    Route::get('/revoke/{user}/{permiso}', [PermissionController::class, 'revokePermission'])->name('permissions.revoke');


    Route::match(['get','post'],'/resumenVentas', [App\Http\Controllers\HomeController::class, 'resumenVentas'])->name('resumenVentas');

    Route::match(['get','post'],'/tesoro', [App\Http\Controllers\TesoroController::class, 'index'])->name('tesoro');

    Route::post('saprod/update', [App\Http\Controllers\SaprodController::class, 'updateSaprodData']);
    Route::get('saprod/export/{codalte}', [App\Http\Controllers\SaprodController::class, 'saprodexport']);

    Route::resource('vendedores', \App\Http\Controllers\SavendController::class);
    Route::controller(\App\Http\Controllers\SavendController::class)->group(function () {
        Route::get('savend/json', 'json');
    });

    Route::controller(\App\Http\Controllers\SafactController::class)->group(function () {
        Route::get('doc/{tipofac}/{numerod}/{fksucu}', 'documentoSafact');
        Route::post('openDoc', 'documentoAjax');
    });

    Route::resource('instancias', \App\Http\Controllers\SainstaController::class);
    Route::controller(\App\Http\Controllers\SainstaController::class)->group(function () {
        Route::get('sainsta/json', 'json');
        Route::post('sainsta/check/lastprod/{codinst}', 'lastprod');
    });

    Route::resource('instpago', \App\Http\Controllers\SatarjController::class);
    Route::controller(\App\Http\Controllers\SatarjController::class)->group(function () {
        Route::get('satarj/json', 'json');
    });

    Route::controller(\App\Http\Controllers\SaclieController::class)->group(function () {
        Route::match(['get','post'],'/clientes/{codclie?}/{tab?}', 'index')->name('buscarclientes');
    });

    Route::controller(\App\Http\Controllers\SaacxcController::class)->group(function () {
        Route::match(['get','post'],'cxc/{id?}', 'saacxc')->name('saacxc');
        Route::post('/cxclist', 'cxclist');
        Route::post('/cxcabonarweb', 'cxcabonarweb');
    });

    Route::resource('proveedores', \App\Http\Controllers\SaprovController::class);
    Route::controller(\App\Http\Controllers\SaprovController::class)->group(function () {
        Route::get('saprov/json', 'json');
    });

    Route::resource('productos', \App\Http\Controllers\SaprodController::class);
    Route::controller(\App\Http\Controllers\SaprodController::class)->group(function () {
        Route::get('saprod/json', 'json');
        Route::post('saprod/check/codprod/{codprod}', 'checkcodprod');
        Route::post('saprod/home/busqueda', 'busquedaHomeProd');
        Route::match(['get','post'],'existencias', 'existencias');
        Route::post('reporte/existen/php', 'existenciasphp');
        Route::match(['get','post'],'ventas/productos/sucursales', 'productossucursales');
        Route::post('saprod/viewprodinstsanciascodalte', 'viewprodinstsanciascodalte');
        Route::match(['get','post'],'/operaciones/{codprod?}', 'index');
    });

    Route::resource('depositos', \App\Http\Controllers\SadepoController::class);
    Route::controller(\App\Http\Controllers\SadepoController::class)->group(function () {
        Route::get('sadepo/json', 'json');
    });

    Route::match(['get','post'],'/reporte/inventarios',     [App\Http\Controllers\SaprodController::class, 'inventarios'])->name('inventarios');

    Route::match(['get','post'],'/reporte/instpagobs',      [App\Http\Controllers\SatarjController::class, 'instpagobs'])->name('instpagobs');
    Route::match(['get','post'],'/reporte/instpagodolares', [App\Http\Controllers\SatarjController::class, 'instpagodolares'])->name('instpagodolares');

    Route::match(['get','post'],'/reporte/venta', [App\Http\Controllers\HomeController::class, 'reporteventa'])->name('reporteventa');
    Route::post('/reporte/venta/sucu', [App\Http\Controllers\HomeController::class, 'reporteventasucu'])->name('reporteventasucu');
    Route::get('/cambiarcomercial/{comercialid}', [App\Http\Controllers\HomeController::class, 'cambiarcomercial'])->name('cambiarcomercial');

    Route::match(['get','post'],'/', [App\Http\Controllers\HomeController::class, 'index'])->name('index');

    Route::match(['get','post'],'/index', [App\Http\Controllers\HomeController::class, 'index'])->name('index');
    Route::get('logout', [TonerController::class, 'logout']);

    Route::get('{any}', [TonerController::class, 'index']);
});

Route::controller(\App\Http\Controllers\CwtransferenciasController::class)->group(function () {

    Route::get('transferencias/cambiarstatus/{Cwtransferencia}', 'cambiarstatus');
    Route::get('transferencias/validar/{Cwtransferencia}', 'validar');
});

