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
use App\Http\Controllers\SaprovController;
use App\Http\Controllers\SaprodImagenController;
use App\Http\Controllers\CwtokenController;
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

    Route::resource('tokens',  CwtokenController::class);
    Route::prefix('tokens')->group(function () {
        Route::get('/', [CwtokenController::class, 'reportetokens'])->name('reportetokens');
        Route::post('/', [CwtokenController::class, 'reportetokens']);
        Route::post('/store', [CwtokenController::class, 'store'])->name('tokens.store');
        Route::post('/update', [CwtokenController::class, 'tokenupdate'])->name('token.update');
        Route::post('/generar-auto', [CwtokenController::class, 'generarTokenAuto']);
        Route::post('/new', [CwtokenController::class, 'newtoken']);
        Route::get('/export', [CwtokenController::class, 'export']);
        Route::delete('/{id}', [CwtokenController::class, 'destroy']);
        Route::post('/bulk-delete', [CwtokenController::class, 'bulkDelete']);
    });

    Route::get('/buscarproducto/{codprod}/{comercial}', [\App\Http\Controllers\SaprodController::class, 'buscarproductoget'])->name('buscarproductoget');

    Route::controller(SasucursalController::class)->group(function () {
        Route::post('sascursal/bancos', 'bancos');
    });

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

    Route::get('/doc/buscar', [SafactController::class, 'buscarFactura'])->name('documento.buscar');

    Route::resource('instancias', \App\Http\Controllers\SainstaController::class);
    Route::controller(\App\Http\Controllers\SainstaController::class)->group(function () {
        Route::get('sainsta/json', 'json')->name('sainsta.json');
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

    Route::resource('proveedores', SaprovController::class);

    Route::controller(SaacxcController::class)->group(function () {
        Route::match(['get','post'],'cxc/{id?}', 'saacxc')->name('saacxc');
        Route::post('/cxclist', 'cxclist');
        Route::post('/cxcabonarweb', 'cxcabonarweb');
        Route::post('/cxc/clientes-por-sucursal', 'clientesPorSucursal');
        Route::post('/cxcdescuento',  'aplicarDescuento')->name('cxcdescuento');
    });

    Route::post('proveedores/compras-historicas-producto', [SaprovController::class, 'comprasHistoricasProducto'])
        ->name('proveedores.compras-historicas-producto');
    Route::post('proveedores/compras-diarias-producto', [SaprovController::class, 'comprasDiariasProducto'])
        ->name('proveedores.compras-diarias-producto');
    Route::post('proveedores/ventas-diarias-producto', [SaprovController::class, 'ventasDiariasProducto'])
        ->name('proveedores.ventas-diarias-producto');
    Route::get('proveedores/cuentas-por-pagar/resumen-general', [SaprovController::class, 'getResumenGeneralCuentasPorPagar'])
        ->name('proveedores.cuentas-por-pagar.resumen-general');
    Route::post('/proveedores/producto/quick-update', [SaprovController::class, 'quickUpdateProducto'])
        ->name('proveedores.producto.quick-update');
    Route::get ('/proveedores/debug/{codprov}/{codprod}', [SaprovController::class, 'debug'])->name('proveedores.debug');

    Route::get('/productos/{codprod}/datos-edit', [SaprodController::class, 'getDatosEdit'])
        ->name('productos.datos-edit');
    Route::post('/proveedores/buscarPredictivo', [SaprovController::class, 'buscarPredictivo'])->name('proveedores.buscarPredictivo');
    Route::get ('/proveedores/{codprov}/productos-panel', [SaprovController::class, 'productosPanel'])->name('proveedores.productos-panel');
    Route::post('/proveedores/{codprov}/productos-panel', [SaprovController::class, 'productosPanel'])->name('proveedores.productos-panel.post');
    Route::get('proveedores/{codprov}/cuentas-por-pagar', [SaprovController::class, 'getCuentasPorPagar'])
        ->name('proveedores.cuentas-por-pagar');


    Route::controller(SaprovController::class)->group(function () {
        Route::get ('saprov/json', 'json');
        Route::match(['get','post'],'/proveedores/{codprov?}/{tab?}', 'index')->name('proveedores.index');
        Route::post('proveedoresupdate', 'proveedoresupdate')->name('proveedoresupdate');
        Route::post('/proveedores/marcar-pagado', 'marcarPagado')->name('proveedores.marcar-pagado');
    });

    Route::get('proveedores/pagos/pendientes', [SaprovController::class, 'pagosPendientes'])->name('proveedores.pagos-pendientes');
    Route::get('proveedores-json', [SaprovController::class, 'json'])->name('proveedores.json');

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
        Route::get( '/existencia/cauchos', 'existenciascauchos');
        Route::post( '/existencia/cauchos/modelos', 'existenciascauchosModelos');
        Route::get( '/existencia/cauchos/modelos/{inspadre}', 'existenciascauchosModelos');
    });

    Route::prefix('productos-imagenes')->name('productos.imagenes.')->group(function () {
        Route::get('/{codprod}', [SaprodImagenController::class, 'getImagenes'])->name('get');
        Route::get('/{codprod}/principal', [SaprodImagenController::class, 'getImagenPrincipal'])->name('get-principal');
        Route::post('/upload', [SaprodImagenController::class, 'upload'])->name('upload');
        Route::post('/upload-multiple', [SaprodImagenController::class, 'uploadMultiple'])->name('upload-multiple');
        Route::delete('/{id}', [SaprodImagenController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/set-principal', [SaprodImagenController::class, 'setPrincipal'])->name('set-principal');
        Route::post('/{id}/set-thumbnail', [SaprodImagenController::class, 'setThumbnail'])->name('set-thumbnail');
        Route::post('/{id}/set-icono', [SaprodImagenController::class, 'setIcono'])->name('set-icono');
        Route::put('/{id}/tipo', [SaprodImagenController::class, 'updateTipo'])->name('update-tipo'); // Ruta para cambiar tipo
        Route::post('/update-order', [SaprodImagenController::class, 'updateOrder'])->name('update-order');
    });

    Route::resource('depositos', \App\Http\Controllers\SadepoController::class);
    Route::controller(\App\Http\Controllers\SadepoController::class)->group(function () {
        Route::get('sadepo/json', 'json');
    });

    Route::get('/productos/plantilla', [SaprodController::class, 'descargarPlantilla'])->name('productos.plantilla');
    Route::post('/productos/importarcrear', [SaprodController::class, 'importarcrear'])->name('productos.importarcrear');
    Route::post('saprod/update', [SaprodController::class, 'updateSaprodData']);
    Route::get('saprod/export/{codalte}', [SaprodController::class, 'saprodexport']);
    Route::post('/productos/validar-codigo', [SaprodController::class, 'validarCodigo'])->name('productos.validar-codigo');


    Route::match(['get','post'],'/reporte/inventarios',     [App\Http\Controllers\SaprodController::class, 'inventarios'])->name('inventarios');

    Route::match(['get','post'],'/reporte/instpagobs',      [App\Http\Controllers\SatarjController::class, 'instpagobs'])->name('instpagobs');
    Route::match(['get','post'],'/reporte/instpagodolares', [App\Http\Controllers\SatarjController::class, 'instpagodolares'])->name('instpagodolares');

    Route::match(['get','post'],'/reporte/venta', [App\Http\Controllers\HomeController::class, 'reporteventa'])->name('reporteventa');
    Route::post('/reporte/venta/sucu', [App\Http\Controllers\HomeController::class, 'reporteventasucu'])->name('reporteventasucu');
    Route::get('/cambiarcomercial/{comercialid}', [App\Http\Controllers\HomeController::class, 'cambiarcomercial'])->name('cambiarcomercial');

    Route::get('/bancos/{id}/moneda', function($id) {
        $banco = \App\Models\Cwbancos::find($id);
        if ($banco->bs)      return response()->json(['moneda' => 'BS']);
        if ($banco->dolares) return response()->json(['moneda' => 'USD']);
        if ($banco->pesos)   return response()->json(['moneda' => 'COP']);
        return response()->json(['moneda' => 'BS']);
    });

    Route::match(['get','post'],'/', [App\Http\Controllers\HomeController::class, 'index'])->name('index');

    Route::match(['get','post'],'/index', [App\Http\Controllers\HomeController::class, 'index'])->name('index');
    Route::get('logout', [TonerController::class, 'logout']);

    Route::get('{any}', [TonerController::class, 'index']);
});

Route::controller(CwtransferenciasController::class)->group(function () {
    Route::get('transferencias/cambiarstatus/{Cwtransferencia}', 'cambiarstatus');
    Route::get('transferencias/validar/{Cwtransferencia}', 'validar')->name('transferencias.validar');
});

