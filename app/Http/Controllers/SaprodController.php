<?php

namespace App\Http\Controllers;

use App\Exports\SaprodExport;
use App\Imports\SaprodUpdate;
use App\Models\Sacomercial;
use App\Models\Saexis;
use App\Models\Sainsta;
use App\Models\Saitemfac;
use App\Models\Saprod;
use App\Models\Saprodsucursal;
use App\Models\Sasucursal;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Maatwebsite\Excel\Facades\Excel;

class SaprodController extends Controller
{
    public function buscarproductoget($codprod, $comercial){

        $producto   = Saprod::where(['codprod'=>$codprod, "comercial" => $comercial])->first();
        session(['comercialid' => $comercial]);
        if(isset($producto) and isset($producto->id)){
            $instancias = Sainsta::selectRaw("concat( repeat('&nbsp;',((nivel-1)*4)), Descrip ) as label, descrip, id, nivel, codinst ")
                ->with(['padre'])
                ->where('comercial',$comercial)
                ->orderBy('codalte','asc')->get();

            $id = $producto->id;
            return view('product-edit', compact('instancias','producto', 'id'));
        }else{
            return response()->redirectTo('index');
        }

    }

    public function saprodexport($codalte)
    {
        $file = Excel::download(new SaprodExport($codalte), 'productos.xlsx');

        return $file;
    }

    public function inventarios(Request $request){
        $comercialid = session('comercialid');
        if(!$comercialid) {
            session(['comercialid' => 1]);
            $comercialid = 1;
        }

        $sqlcostoinv = "SELECT sum((a.preciod)*b.existen) as suma, c.descrip
								from   saprod a , saexis b, sasucursal c
								where  a.codprod = b.codprod
                                and b.fk_sucursal = c.id
								and a.comercial = $comercialid
                                and c.fk_comercial = $comercialid
                            group by  c.descrip order by c.descrip
								";

        $costoinven = DB::select($sqlcostoinv);

        return view('reporteInventarios', compact('costoinven') );
    }

    public function updateSaprodData(Request $request)
    {
        $request->validate([
            'import_file' => [
                'required',
                'file'
            ],
        ]);

        Excel::import(new SaprodUpdate(), $request->file('import_file'));

        return redirect()->back()->with('status', 'Archivo Procesado Exitosamente');
    }

    public function index(Request $request)
    {
        $comercialid = session('comercialid');
        if(!$comercialid) {
            session(['comercialid' => 1]);
            $comercialid = 1;
        }

        $sucursales = Sasucursal::where("fk_comercial", $comercialid)->get();

        $fechasaux      = '';
        $operacionesrep = '';

        $fechasreport   = (isset($request->fechasreport))? $request->fechasreport : '';
        $codprod        = (isset($request->codprod))? $request->codprod : '';
        $fechashoy      =  Carbon::now()->format('d/m/Y');
        $nofilterdate = 0;

        if(!$fechasreport) {
            $nofilterdate = 1;
            $fechasreport = $fechashoy;
        }

        $fechasaux = str_replace(' ','',$fechasreport);
        $fec1 = $fec2 = '';

        if(strpos($fechasaux,"to"))
            list($fec1, $fec2) = explode("to",$fechasaux);
        else {
            if(!$nofilterdate) {
                list($d1, $m1, $y1) = explode("/", $fechasreport);
                $fec1 = "$d1/$m1/$y1";
                $fec2 = $fec1;
                $fechasreport = "$fec1 to $fec2";
            }else{
                list($d1, $m1, $y1) = explode("/", $fechasreport);
                $fec1 = "$d1/$m1/$y1";
                $fec2 = "$d1/$m1/$y1";
                $fechasreport = "$fec1 to $fec2";
            }
        }

        list($d1,$m1,$y1) = explode("/",$fec1);
        list($d2,$m2,$y2) = explode("/",$fec2);

        $fec1 = "$y1-$m1-$d1";
        $fec2 = "$y2-$m2-$d2";

        $instancias = Sainsta::selectRaw("  Descrip as label, descrip, id, nivel, codinst , codalte")
                               ->with(['padre','hijos',  'productos'])
                               ->where('comercial',$comercialid)
                               ->orderBy('codalte','asc')
                               ->get();

        if(isset($codprod) and $codprod !=''){

            $compras = DB::table('saitemcom')
                ->select([
                    'id',
                    'tipocom as tipo',
                    'numerod',
                    DB::raw("date_format(fechae,'%d/%m/%Y') as fecha"),
                    DB::raw('(cantidad*signo) as cantidad'),
                    'fk_sucursal',
                    'preciod as costo',
                    'costod as precio',
                    'codubic as dep1',
                    DB::raw("'' as dep2"),
                    'descrip1 as descripcion',
                    DB::raw("'COMPRA' as tipo_movimiento")
                ])
                ->where('coditem', $codprod)
                ->whereBetween('fechae', ["$fec1 00:00:00", "$fec2  23:55:00"]);

            $ventas = DB::table('saitemfac')
                ->select([
                    'id',
                    'TipoFac as tipo',
                    'numerod',
                    DB::raw("date_format(fechae,'%d/%m/%Y') as fecha"),
                    DB::raw('(cantidad*signo) as cantidad'),
                    'fk_sucursal',
                    'preciod as costo',
                    'costod as precio',
                    'codubic as dep1',
                    DB::raw("'' as dep2"),
                    'Descrip1 as descripcion',
                    DB::raw("'VENTA' as tipo_movimiento")
                ])
                ->where('CodItem', $codprod)
                ->whereBetween('FechaE', ["$fec1 00:00:00", "$fec2  23:55:00"]);

            $operaciones = DB::table('saitemopi')
                ->select([
                    'id',
                    'tipoopi as tipo',
                    'numerod',
                    DB::raw("date_format(fechae,'%d/%m/%Y') as fecha"), // Corregí el campo fecha aquí
                    DB::raw('(cantidad*signo) as cantidad'),
                    'fk_sucursal',
                    'preciod as costo',
                    DB::raw('0 as precio'),
                    'codubic as dep1',
                    'codubic2 as dep2',
                    'Descrip1 as descripcion',
                    DB::raw("'OPERACION_INTERNA' as tipo_movimiento")
                ])
                ->where('CodItem', $codprod)
                ->whereBetween('FechaE', ["$fec1 00:00:00", "$fec2  23:55:00"]);

            $operacionesrep = $compras->union($ventas)->union($operaciones)
                ->orderBy('fecha', 'asc')
                ->get();

           // dd($operacionesrep);

        }



        return view('product-list', compact('instancias', 'sucursales', 'codprod', 'operacionesrep', 'fechasreport') );
    }

    public function existencias()
    {
        $comercial  = session('comercialid') ;
        if(!$comercial) {
            session(['comercialid' => 1]);
            $comercial = 1;
        }
        $instancias = '';

        $instancias = Sainsta::selectRaw("  Descrip as label, descrip, id, nivel, codinst , codalte, insPadre")
                               ->where('comercial',$comercial)
                               ->orderBy('descrip','asc')
                               ->get();

        $sucursales = Sasucursal::where("fk_comercial", $comercial)->get();

        return view('existenciasInstancias', compact( 'sucursales', 'instancias', 'comercial') );
    }

    public function existenciasphp(Request $request)
    {
        $codinst    = $request->codinst;
        $fksucursal = $request->fksucursal;


        $comercial  = session('comercialid') ;
        if(!$comercial) {
            session(['comercialid' => 1]);
            $comercial = 1;
        }

        $instancias = Sainsta::selectRaw("  Descrip as label, descrip, id, nivel, codinst , codalte, insPadre")
                               ->where('comercial',$comercial)
                               ->orderBy('descrip','asc')
                               ->get();
        $insPadre = 0;

        $sucursales = Sasucursal::where("fk_comercial", $comercial)->get();

        $instanciaselected = '';
        foreach ($instancias as $instancia){
            if($instancia->codinst == $codinst){
                $instanciaselected = $instancia;
                $insPadre = $instancia->insPadre;
                break;
            }
        }
        return view('existenciasInstanciasphp', compact('fksucursal', 'insPadre', 'codinst', 'sucursales', 'instancias', 'instanciaselected', 'comercial') )->render();
    }

    public function json()
    {
        $comercial  = session('comercialid') ;
        $all = Saprod::where('comercial',$comercial)->with(['instancia'])->orderBy('descrip','asc')->get();
        $aux = [];
        $productos = [];
        $noimage = URL::asset('build/images/noimagen.jpg');
        foreach ($all as $item){
            $aux = [
                "id"            => "$item->id",
                "price"         => "$item->costod3",
                "exdecimal"     => "$item->exdecimal",
                "image"         => (isset($item->productImg))? '': $noimage,
                "productTitle"  => "$item->descrip",
                "category"      => $item->instancia->descrip
            ];

            array_push($productos,$aux);
        }
        return response()->json($productos );
    }

    public function productossucursales(Request $request)
    {
        $comercialid  = session('comercialid') ;
        $fechasreport = $request->fechasreport;
        $fechashoy    =  Carbon::now()->format('d/m/Y');
        $nofilterdate = 0;

        if(!$fechasreport) {
            $nofilterdate = 1;
            $fechasreport = $fechashoy;
        }

        $fechasaux = str_replace(' ','',$fechasreport);
        $fec1 = $fec2 = '';

        if(strpos($fechasaux,"to"))
            list($fec1, $fec2) = explode("to",$fechasaux);
        else {
            if(!$nofilterdate) {
                list($d1, $m1, $y1) = explode("/", $fechasreport);
                $fec1 = "$d1/$m1/$y1";
                $fec2 = $fec1;
                $fechasreport = "$fec1 to $fec2";
            }else{
                list($d1, $m1, $y1) = explode("/", $fechasreport);
                $fec1 = "$d1/$m1/$y1";
                $fec2 = "$d1/$m1/$y1";
                $fechasreport = "$fec1 to $fec2";
            }
        }

        list($d1,$m1,$y1) = explode("/",$fec1);
        list($d2,$m2,$y2) = explode("/",$fec2);

        $fec1 = "$y1-$m1-$d1";
        $fec2 = "$y2-$m2-$d2";

        $listado = Saitemfac::whereRaw("TipoFac in ('A','B','Z','W')")
                    ->selectRaw("fk_sucursal, coditem, SUM(Cantidad*Signo) as salidas")
                    ->with(['sucursal','producto.instancia' => function($q) { $q->orderBy('codalte', 'asc'); }])
                    ->where('esserv',0)
                    ->whereHas('sucursal.comercial', function($q) use ($comercialid) {
                        $q->where('fk_comercial',$comercialid);
                    })
                    ->whereBetween('FechaE', [$fec1.' 00:00:00.00', $fec2.' 23:58:22.00'])
                    ->groupBy(['fk_sucursal','coditem'])->orderBy('fk_sucursal')->get();

        $sucursales = [];
        $cantidadprod = [];
        $itemventas = [];

        if(isset($listado))
            foreach($listado as $prodsuc){

                 if(!isset($sucursales[$prodsuc->sucursal->id])){
                     $sucursales[$prodsuc->sucursal->id] = $prodsuc->sucursal->descrip;
                 }

                 if(!isset($cantidadprod[$prodsuc->coditem.$prodsuc->sucursal->id])){
                    $cantidadprod[$prodsuc->coditem.$prodsuc->sucursal->id]=0;
                 }

                 $cantidadprod[$prodsuc->coditem.$prodsuc->sucursal->id] += $prodsuc->salidas;
                 $itemventas[$prodsuc->producto->instancia->descrip][$prodsuc->coditem]['descrip']   = $prodsuc->producto->descrip;
                 $itemventas[$prodsuc->producto->instancia->descrip][$prodsuc->coditem]['exdecimal'] = $prodsuc->producto->exdecimal;

            }

        asort($sucursales);

        return view('productosSucursales', compact('fechasreport', 'sucursales',   'itemventas', 'cantidadprod'));
    }

    public function busquedaHomeProd(Request $request)
    {
        $busqueda = $request->busqueda;
        $busqueda = str_replace("\"", "", $busqueda);
        $busqueda = str_replace("'", "", $busqueda);
        $busqueda = str_replace("*", " ", $busqueda);
        $vector = explode(" ", $busqueda);

        if ($vector ) {
            $numerito = 0;
            $cadena   = '';
            foreach ($vector as $value) {
                if ($numerito > 0) {
                    $cadena  .= ' AND ';
                }
                $cadena  .= "(codprod like '%$value%' or descrip like '%$value%' or refere like '%$value%' or marca like '%$value%' or descrip2 like '%$value%')";
                $numerito++;
            }
        }

        $comercial = session('comercialid') ;
        $productos = Saprod::where('comercial',$comercial)->whereRaw($cadena)->orderBy('updated_at','desc')->limit(60)->get();

        return view('layouts.ajaxbusqueda',compact('productos'))->render();
    }

    public function saprodsucursal(Request $request)
    {
        $sucursalid = str_replace("300", "", $request->sucursal);
        $productos = $request->productos;
        $productos = json_decode($productos);

        if (isset($productos))
            foreach ($productos as $producto){
                $aux = Saprodsucursal::where(['codprod' => $producto->codprod, 'fk_sucursal'=>$sucursalid])->first();
                if(!$aux){
                    $rel              = new Saprodsucursal();
                    $rel->codprod     = $producto->codprod;
                    $rel->fk_sucursal = $sucursalid;
                    $rel->save();
                }
            }

        return response()->json(['success'=>'success']);
    }

    public function list(Request $request)
    {
        $sucursalid = str_replace("300","",$request->sucursal);
        $sucursal   = Sasucursal::find($sucursalid);
        $comercial  = $sucursal->fk_comercial;

        $productos = Saprod::where('comercial',$comercial)
            ->whereRaw("codprod not in (select codprod from saprodsucursal where fk_sucursal=$sucursalid )")->get()->take(50);

        return response()->json(['success'=>'success', 'newproductos' => $productos]);
    }

    public function productosinstsancias(Request $request)
    {
        $sucursalid  = str_replace("300","",$request->sucursal);
        $sucursal    = Sasucursal::find($sucursalid);
        $comercialid = $sucursal->fk_comercial;
        $codinst     = $request->codinst;

        $sqlcostoinv = "SELECT a.preciodant, a.preciod, a.descrip, a.codprod, e.codubic, b.existen, e.descrip as deposito
								from   saprod a , saexis b, sasucursal c, sainsta d, sadepo e
								where  a.codprod = b.codprod
                                and b.fk_sucursal = c.id
								and b.codubic = e.codubic
                                and c.fk_comercial = $comercialid
								and d.codinst = a.codinst
                                and a.codinst = $codinst
								and e.comercial = $comercialid
								and b.existen > 0
                        order by a.descrip
								";

        $listado = DB::select($sqlcostoinv);

        return response()->json(['success'=>'success', 'listado' => $listado]);

    }

    public function productosinstsanciascodalte(Request $request)
    {
        $sucursalid  = str_replace("300","",$request->sucursal);
        $sucursal    = Sasucursal::find($sucursalid);
        $comercialid = $sucursal->fk_comercial;
        $codalte     = $request->codalte;
        $len         = strlen($codalte);

        $sqlcostoinv = "SELECT a.preciodant, a.preciodpro, a.preciod, a.descrip, a.codprod, e.codubic, b.existen, e.descrip as deposito
								from   saprod a , saexis b, sasucursal c, sainsta d, sadepo e
								where  a.codprod = b.codprod
                                and b.fk_sucursal = c.id
								and b.codubic   = e.codubic
                                and c.fk_comercial = $comercialid
								and a.comercial = $comercialid
								and d.comercial = $comercialid
								and e.comercial = $comercialid
								and d.codinst   = a.codinst
                                and left(d.codalte,$len) = '$codalte'
								and b.existen > 0
                                order by a.descrip
								";

        $listado = DB::select($sqlcostoinv);

        return response()->json(['success'=>'success', 'listado' => $listado, 'sqlcostoinv' => $sqlcostoinv]);

    }

    public function viewprodinstsanciascodalte(Request $request)
    {
        $comercial  = session('comercialid') ;
        if(!$comercial) {
            session(['comercialid' => 1]);
            $comercial = 1;
        }

        $codalte     = $request->codalte;
        $busqueda    = $request->busqueda;
        $len         = strlen($codalte);

        $busqueda = str_replace("\"", "", $busqueda);
        $busqueda = str_replace("'",  "", $busqueda);
        $busqueda = str_replace("*", " ", $busqueda);
        $vector = explode(" ", $busqueda);

        if ($vector ) {
            $numerito = 0;
            $cadena   = '';
            foreach ($vector as $value) {
                if ($numerito > 0) {
                    $cadena  .= ' AND ';
                }
                $cadena  .= "(a.codprod like '%$value%' or a.descrip like '%$value%' or a.refere like '%$value%' or a.marca like '%$value%' or a.descrip2 like '%$value%')";
                $numerito++;
            }
        }


        if($cadena!='') $cadena = " and ($cadena) ";

        $sqlcostoinv = "SELECT a.preciodant, a.preciodpro, a.preciod, a.descrip, a.codprod, e.codubic, b.existen, e.descrip as deposito
								from saprod a , saexis b, sasucursal c, sainsta d, sadepo e
								where a.codprod    = b.codprod
                                and b.fk_sucursal  = c.id
								and b.codubic      = e.codubic
                                and c.fk_comercial = $comercial
								and a.comercial    = $comercial
								and d.comercial    = $comercial
								and e.comercial    = $comercial
								$cadena
								and d.codinst      = a.codinst
                                and left(d.codalte,$len) = '$codalte'
								and b.existen <> 0
                                order by a.descrip
								";

        $listado = DB::select($sqlcostoinv);

        $productos    = [];
        $deposito     = [];
        $existencias  = [];

        foreach($listado as $producto){

            if(!isset($productos[$producto->codprod]))
                $productos[$producto->codprod] = [];

            $productos[$producto->codprod]['descrip']    = $producto->descrip;
            $productos[$producto->codprod]['preciodpro'] = $producto->preciodpro;
            $productos[$producto->codprod]['preciod']    = $producto->preciod;

            if(!isset($deposito[$producto->codubic]))
                $deposito[$producto->codubic] = $producto->deposito;

            if(!isset($existencias[$producto->codprod][$producto->codubic]))
                $existencias[$producto->codprod][$producto->codubic] = 0;

            $existencias[$producto->codprod][$producto->codubic] = $producto->existen;
        }

        return view('productosallinstsancias', compact('productos', 'deposito', 'existencias') )->render();


    }

    public function listprodubic(Request $request)
    {
        $sucursalid = str_replace("300","",$request->sucursal);
        $sucursal   = Sasucursal::find($sucursalid);
        $comercial  = $sucursal->fk_comercial;
        $codprod    = $request->codprod;

        $allsucursa = Sasucursal::where('fk_comercial',$comercial)->get();
        $auxsucu    = [];

        foreach ($allsucursa as $sucu){
            array_push( $auxsucu, $sucu->id);
        }
        $auxsucu = implode(',' , $auxsucu);

        $existencias = Saexis::whereRaw("fk_sucursal in ($auxsucu) and codprod='$codprod' and existen > 0")
            ->orderBy('codubic')->get();

        return response()->json(['success'=>'success', 'existencias' => $existencias]);
    }

    public function listprodubicinv(Request $request)
    {
        $sucursalid = str_replace("300","",$request->sucursal);
        $sucursal   = Sasucursal::find($sucursalid);
        $comercial  = $sucursal->fk_comercial;
        $codprod    = $request->codprod;

        $allsucursa = Sasucursal::where('fk_comercial',$comercial)->get();
        $auxsucu    = [];

        foreach ($allsucursa as $sucu){
            array_push( $auxsucu, $sucu->id);
        }
        $auxsucu = implode(',' , $auxsucu);

        $existencias = Saexis::whereRaw("fk_sucursal in ($auxsucu) and codprod='$codprod' and existen > 0")
            ->orderBy('codubic')->get();

        return response()->json(['success'=>'success', 'existencias' => $existencias]);
    }

    public function create()
    {
        $comercialid  = session('comercialid') ;
        if(!$comercialid)
            $comercialid  = 1;

        $comercial    = Sacomercial::find($comercialid);
        $match        = $comercial->match;

        $instancias = Sainsta::selectRaw("concat( repeat('&nbsp;',((nivel-1)*4)), Descrip ) as label, descrip, id, nivel, codinst ")
            ->with(['padre'])
            ->where('comercial', $match)
            ->orderBy('codalte','asc')->get();

        $last   = '';

        return view('product-create', compact('instancias','last') );
    }

    public function checkcodprod($codprod)
    {
        $check   = 1;
        $comercial = session('comercialid') ;

        $comercial    = Sacomercial::find($comercial);
        $match        = $comercial->match;

        if($codprod != '')
            $product = Saprod::where(['codprod' => $codprod, 'comercial' => $match])->first();

        if(isset($product) and $product->codprod != '')
            $check = 0;

        return response()->json(['check' => $check ]);
    }

    public function store(Request $request)
    {

        $comercial = session('comercialid') ;

        $comercial    = Sacomercial::find($comercial);
        $match        = $comercial->match;

        $comerciales = Sacomercial::where('match',$match)->get();

        foreach ($comerciales as $comercial){
            $newprod = new Saprod();
            $newprod->fill($request->all());
            $newprod->codprod   = substr($request->codprod,0,15);
            $newprod->comercial = $comercial->id;
            $newprod->save();
        }
        return redirect()->route('productos.index');
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        $producto   = Saprod::find($id);

        $comercialid = session('comercialid') ;

        $comercial    = Sacomercial::find($comercialid);
        $match        = $comercial->match;


        $instancias = Sainsta::selectRaw("concat( repeat('&nbsp;',((nivel-1)*4)), Descrip ) as label, descrip, id, nivel, codinst ")
            ->with(['padre'])
            ->where('comercial',$match)
            ->orderBy('codalte','asc')->get();

        return view('product-edit', compact('instancias','producto', 'id'));
    }

    public function update(Request $request, $id)
    {
        $comercialid = session('comercialid') ;

        $comercial    = Sacomercial::find($comercialid);
        $match        = $comercial->match;

        $producto  = Saprod::find($id);
        $producto->fill($request->all());

        if($comercialid == 1 or $comercialid== 3 or $comercialid== 4){
             $producto->esexento = 1;  //// luego ver como manejamos esto
        }

        if(isset($request->preciod)   ) {
            $preciod = $request->preciod;
            $coma = substr_count($preciod, ',');
            $punto = substr_count($preciod, '.');

            if ($coma > 0 and $punto > 0) {
                $preciod = str_replace(".", '', $preciod);
                $preciod = str_replace(",", '.', $preciod);
            }
            if ($coma > 0 and !$punto)
                $preciod = str_replace(",", '.', $preciod);
            $producto->preciod = $preciod;
        }
        /////////////////////////////////////////////
        if(isset($request->preciodpro)) {
            $preciodpro = $request->preciodpro;
            $coma = substr_count($preciodpro, ',');
            $punto = substr_count($preciodpro, '.');

            if ($coma > 0 and $punto > 0) {
                $preciodpro = str_replace(".", '', $preciodpro);
                $preciodpro = str_replace(",", '.', $preciodpro);
            }
            if ($coma > 0 and !$punto)
                $preciodpro = str_replace(",", '.', $preciodpro);
            $producto->preciodpro = $preciodpro;
        }
        /////////////////////////////////////////////
        if(isset($request->preciodant)) {
            $preciodant = $request->preciodant;
            $coma = substr_count($preciodant, ',');
            $punto = substr_count($preciodant, '.');

            if ($coma > 0 and $punto > 0) {
                $preciodant = str_replace(".", '', $preciodant);
                $preciodant = str_replace(",", '.', $preciodant);
            }
            if ($coma > 0 and !$punto)
                $preciodant = str_replace(",", '.', $preciodant);
            $producto->preciodant = $preciodant;
        }
        ////////////////////////////////////////////////////////////////////////////
        if(isset($request->costod) ) {
            $costod = $request->costod;

            $coma = strpos($costod, ',');
            $punto = strpos($costod, '.');

            if ($coma > 0 and $punto > 0) {
                dd($coma);
                $costod = str_replace(".", '', $costod);
                $costod = str_replace(",", '.', $costod);
            }
            if ($coma > 0 and !$punto)
                $costod = str_replace(",", '.', $costod);

            $producto->costod = $costod;
        }
        ////////////////////////////////////////////////////////////////////////////
        if(isset($request->costod2)) {
            $costod2 = $request->costod2;
            $coma = substr_count($costod2, ',');
            $punto = substr_count($costod2, '.');

            if ($coma > 0 and $punto > 0) {
                $costod2 = str_replace(".", '', $costod2);
                $costod2 = str_replace(",", '.', $costod2);
            }
            if ($coma > 0 and !$punto)
                $costod3 = str_replace(",", '.', $costod2);
            $producto->costod2 = $costod2;
        }
        ////////////////////////////////////////////////////////////////////////////
        if(isset($request->costod3)) {
            $costod3 = $request->costod3;
            $coma = substr_count($costod3, ',');
            $punto = substr_count($costod3, '.');

            if ($coma > 0 and $punto > 0) {
                $costod3 = str_replace(".", '', $costod3);
                $costod3 = str_replace(",", '.', $costod3);
            }
            if ($coma > 0 and !$punto)
                $costod3 = str_replace(",", '.', $costod3);
            $producto->costod3 = $costod3;
        }
        ////////////////////////////////////////////////////////////////////////////

        if(!isset($request->exdecimal))
            $producto->exdecimal = 0;

        if(!$request->activo)
            $producto->activo    = 0;

        $producto->save();

        $codprod  = $producto->codprod;


        $otrosprod = Saprod::where(['codprod'=>$codprod, 'comercial' => $match])->get();
        foreach ($otrosprod as $otro){
            $otro->descrip  = $request->descrip;
            $otro->descrip2 = $request->descrip2;
            $otro->descrip3 = $request->descrip3;
            $otro->descrip4 = $request->descrip4;
            $otro->marca    = $request->marca;
            $otro->codinst  = $request->codinst;
            $otro->refere   = $request->refere;
            $otro->save();
        }

        $prodsucursal = Saprodsucursal::with('producto')->where('codprod', $producto->codprod)->get();
        if($prodsucursal)
            foreach ($prodsucursal as $item){
                if($item->producto->comercial == $match)
                    $item->delete();
            }

        $comerciales = Sacomercial::where('match', $match)->get();

        foreach ($comerciales as $comercial){

            $product = Saprod::where(['codprod' => $codprod, 'comercial' => $comercial->id])
                ->first();

            if(isset($product) and isset($product->codprod) and $product->codprod != ''){

            }else{
                $newprod = new Saprod();
                $newprod->fill($request->all());
                $newprod->codprod   = $codprod;
                $newprod->preciod   = 0;
                $newprod->preciodpro= 0;
                $newprod->costod    =  0;
                if($comercial->id == 1 or $comercial->id == 2 or $comercial->id == 3){  $newprod->esexento = 1; }else{$newprod->esexento = 0;}
                $newprod->costod2   =  0;
                $newprod->costod3   =  0;
                $newprod->comercial = $comercial->id;
                $newprod->save();
            }
        }

        return redirect()->route('productos.edit',$id);
    }

    public function destroy($id)
    {
        //
    }
}
