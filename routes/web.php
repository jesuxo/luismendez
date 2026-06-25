<?php

use App\Http\Controllers\TonerController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\CwtransferenciasController;
use App\Http\Controllers\SaprodController;
use App\Http\Controllers\SaacxcController;
use App\Http\Controllers\ComercialDashboardController;
use App\Http\Controllers\SafactController;
use App\Http\Controllers\SacompController;
use App\Http\Controllers\UserSucursalController;
use App\Http\Controllers\ImagenController;
use App\Http\Controllers\SasucursalController;
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
    // Ruta para cambiar de comercial
    Route::get('/cambiarcomercial/{comercialId}', [ComercialDashboardController::class, 'cambiarComercial'])
        ->name('comercial.cambiar');

    // Ruta para obtener comerciales disponibles (API)
    Route::get('/comerciales/disponibles', [ComercialDashboardController::class, 'getComercialesDisponibles'])
        ->name('comerciales.disponibles');
});


Route::middleware(['auth'])->group(function () {

    Route::resource('tokens', \App\Http\Controllers\CwtokenController::class);
    Route::controller(\App\Http\Controllers\CwtokenController::class)->group(function () {
        Route::match(['get','post'],'reporte/tokens', 'reportetokens')->name('reportetokens');
        Route::post('token/update', 'tokenupdate')->name('tokenupdate');
    });

    Route::get('/buscarproducto/{codprod}/{comercial}', [\App\Http\Controllers\SaprodController::class, 'buscarproductoget'])->name('buscarproductoget');

    Route::post('/sascursal/bancos', [SasucursalController::class, 'getBancos'])->name('sucursal.bancos');

    Route::resource('transferencias', CwtransferenciasController::class);
    Route::controller( CwtransferenciasController::class)->group(function () {

        Route::match(['get','post'],'reporte/transferencias', 'reportetransferencias')->name('reportetransferencias');
        Route::post('transferencias/json/{busquedatransf}/{status}/{fechas}', 'json');
        Route::get('transferencias/status/{status}', 'filtrarstatus');
        Route::post('transferencias/pendienteAgain', 'pendienteAgain');
        Route::post('transferencias/DescargarAgain', 'DescargarAgain');
        Route::post('transferencias/verificar', 'verificar');
        Route::match(['get','post'],'transferencia/informacion', 'informacion');
        Route::post('/transferencias/verificar-tiempo-real', 'verificarTiempoReal')->name('transferencias.verificar.tiemporeal');
        Route::post('/transferencias/buscar-numeros-similares', 'buscarNumerosSimilares')->name('transferencias.buscar.numeros');
    });

    Route::get('transferencias/exportar/excel', [CwtransferenciasController::class, 'exportarExcel'])->name('transferencias.exportar.excel');
    Route::get('transferencias/exportar/estadisticas', [CwtransferenciasController::class, 'exportarEstadisticas'])->name('transferencias.exportar.estadisticas');
    Route::get('/transferencias/data', [CwtransferenciasController::class, 'getTransferenciasData'])->name('transferencias.data');
    Route::get('imagen/transferencia/{id}', [ImagenController::class, 'transferencia'])->name('imagen.transferencia');
    Route::get('transferencias/categorias/{q}', [CwtransferenciasController::class, 'getCategorias'])->name('transferencias.categorias');


    Route::get('/verpermisos/{id?}', [PermissionController::class, 'showForm'])->name('permissions.assign');
    Route::post('/verpermisos', [PermissionController::class, 'assign']);
    // Crear nuevo permiso
    Route::post('/create/permissions', [PermissionController::class, 'create'])->name('permissions.create');
    Route::get('/revoke/{user}/{permiso}', [PermissionController::class, 'revokePermission'])->name('permissions.revoke');


    Route::prefix('cxcweb')->name('cxcweb.')->group(function () {
        Route::get('/instrumentos', [SaacxcController::class, 'getInstrumentosPago'])->name('instrumentos');
        Route::post('/procesar-pago-web', [SaacxcController::class, 'procesarPagoWeb'])->name('procesar.pago.web');
    });

    Route::match(['get','post'],'/reporte/compra', [SacompController::class, 'reportecompra'])->name('reportecompra');
    Route::post('/compras/documento-ajax', [SacompController::class, 'documentoAjax'])->name('compras.documento-ajax');

    // Ruta para búsqueda de facturas
    Route::get('/buscar-factura/{tipo}/{numero}', [SafactController::class, 'buscarFacturaPorNumero'])
        ->name('documento.buscar.ajax');

    Route::get('/buscar-factura/{tipo}/{numero}', [App\Http\Controllers\SafactController::class, 'buscarFacturaPorNumero'])
        ->name('documento.buscar.ajax');

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

    Route::prefix('usersucursal')->group(function () {
        Route::get('/', [UserSucursalController::class, 'index'])->name('usersucursal.index');
        Route::get('/usuarios', [UserSucursalController::class, 'getUsersConSucursales'])->name('usersucursal.usuarios');
        Route::get('/sucursales', [UserSucursalController::class, 'getAllSucursales'])->name('usersucursal.sucursales');
        Route::get('/sucursales-asignadas/{userId}', [UserSucursalController::class, 'getSucursalesAsignadasPorUsuario']);
        Route::get('/usuarios-por-sucursal/{sucursalId}', [UserSucursalController::class, 'getUsuariosPorSucursal']);
        Route::post('/asignar', [UserSucursalController::class, 'asignarSucursal'])->name('usersucursal.asignar');
        Route::post('/quitar', [UserSucursalController::class, 'quitarSucursal'])->name('usersucursal.quitar');
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

    Route::resource('productos', SaprodController::class);
    Route::controller(SaprodController::class)->group(function () {
        Route::post('saprod/listprodubiccompany', 'listprodubiccompany')->name('saprod.listprodubiccompany');
        Route::get('saprod/json', 'json');
        Route::match(['get','post'],'sugerir-transferencias', [SaprodController::class, 'sugerirTransferencias'])->name('sugerir-transferencias');
        Route::post('saprod/check/codprod/{codprod}', 'checkcodprod');
        Route::post('saprod/home/busqueda', 'busquedaHomeProd');
        Route::match(['get','post'],'existencias', 'existencias');
        Route::post('reporte/existen/php', 'existenciasphp');
        Route::match(['get','post'],'ventas/productos/sucursales', 'productossucursales');
        Route::match(['get','post'],'ventas/resultado', 'resultadosucursales');
        Route::post('saprod/viewprodinstsanciascodalte', 'viewprodinstsanciascodalte');
        Route::match(['get','post'],'/operaciones/{codprod?}', 'index');
        Route::get( '/existencia/cauchos', 'existenciasCelulares');
        Route::post( '/existencia/cauchos/modelos', 'existenciasCelularesModelos');
        Route::get( '/existencia/cauchos/modelos/{inspadre}', 'existenciasCelularesModelos');
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

Route::controller(CwtransferenciasController::class)->group(function () {
    Route::get('transferencias/cambiarstatus/{Cwtransferencia}', 'cambiarstatus');
    Route::get('transferencias/validar/{Cwtransferencia}', 'validar')->name('transferencias.validar');
});

