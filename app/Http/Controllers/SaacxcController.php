<?php

namespace App\Http\Controllers;

use App\Models\Saacxc;
use App\Models\Sainsta;
use App\Models\Saipacxc;
use App\Models\Sasucursal;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SaacxcController extends Controller
{
    public function index()
    {
        //
    }

    public function saacxc(Request $request, $id = null)
    {
        if (!isset($id))
            $id = '';

        $comercial = session('comercialid');

        if (!$comercial) {
            session(['comercialid' => 1]);
            $comercial = 1;
        }

        $cxcprocesos = Saacxc::with('cliente')->whereRaw("descargar > 0")->orderBy('id','desc')->get();

        $sucursales = Sasucursal::where("fk_comercial", $comercial)->orderBy('descrip')->get();

        $sucursalselected = '';
        foreach ($sucursales as $sucursal) {
            if ($sucursal->id == $id) {
                $sucursalselected = $sucursal;
                break;
            }
        }


        $fechasreport = (isset($request->fechasreport)) ? $request->fechasreport : '';

        $fechasaux = str_replace(' ', '', $fechasreport);
        $fecha1 = '';
        $fecha2 = '';
        $d1 = $m1 = $y1 = '';
        $d2 = $m2 = $y2 = '';

        if (strpos($fechasaux, "to")){
            list($fecha1, $fecha2) = explode("to", $fechasaux);
            list($d1, $m1, $y1) = explode("/", $fecha1); $fecha1 = "$y1-$m1-$d1";
            list($d2, $m2, $y2) = explode("/", $fecha2); $fecha2 = "$y2-$m2-$d2";
        }else {
            if($fechasreport  != ''){
                list($d1, $m1, $y1) = explode("/", $fechasreport);
                $fecha1 = "$d1/$m1/$y1";
                $fecha2 = "$d1/$m1/$y1";
                $fechasreport = "$fecha1 to $fecha2";
                $fecha1 = "$y1-$m1-$d1";
                $fecha2 = "$y1-$m1-$d1";
            }
        }

        return view('saacxc', compact('cxcprocesos', 'fecha1','fecha2', 'fechasreport', 'id', 'sucursales', 'sucursalselected', 'comercial') );
    }

    public function cxclist(Request $request)
    {
        $codclie = $request->codclie;
        $fecha1  = (isset($request->fecha1))? $request->fecha1 : '';
        $fecha2  = (isset($request->fecha2))? $request->fecha2 : '';

        $datafechas = '';

        if(isset($fecha1) and isset($fecha2) and $fecha1 != '' and $fecha2 != ''){
            $datafechas = " and  (c.fechat >= '$fecha1 00:00:00.00' and c.fechat <= '$fecha2 23:59:22') ";
        }

        $sqlcostoinv = "
                                                       SELECT d.descrip as sucursal, c.fechat,
                                                        'FACT' AS tipo,
                                                        c.numerod as numero,
                                                        a.descrip AS cliente,
                                                        date_format(c.fechat,'%d/%m/%Y') fecha,
                                                         (c.montodolares) AS credito,
                                                         (c.montodolares - (c.saldo / c.tasadolar)) AS abonado,
                                                        a.codclie,
                                                         (c.saldo / c.tasadolar) AS saldo,
                                                          (IFNULL(
                                                         ((  select totalmontodivisa
                                                            from safact g
                                                            where g.codclie='$codclie'
                                                            and g.numerod= c.numerod
                                                            and c.tipocxc = '10'
                                                            and g.fk_sucursal= c.fk_sucursal
                                                            and g.codclie = a.codclie
                                                            and g.tipofac = 'A'
                                                        )* ((c.saldo/c.tasadolar)/c.montodolares))
                                                    ,0)) as saldodivisa
                                                    FROM
                                                        saclie AS a
                                                    JOIN
                                                        saacxc AS c
                                                        ON c.codclie = a.codclie
                                                    JOIN
                                                        sasucursal AS d
                                                        ON d.id = c.fk_sucursal
                                                    WHERE
                                                        c.Saldo > 10
                                                        AND c.tipocxc IN (10)
                                                        AND c.tasadolar > 0
                                                        AND c.codclie = '$codclie'
                                                        $datafechas

                                                        UNION

                                                        SELECT  d.descrip as sucursal, c.fechat,
                                                        'N.DEB' AS tipo,
                                                        c.numerod as numero,
                                                        a.descrip AS cliente,
                                                        date_format(c.fechat,'%d/%m/%Y') fecha,
                                                         (c.montodolares) AS credito,
                                                         (c.montodolares - (c.saldo / c.tasadolar)) AS abonado,
                                                        a.codclie,
                                                         (c.saldo / c.tasadolar) AS saldo,
                                                         0 as saldodivisa
                                                    FROM
                                                        saclie AS a
                                                    JOIN
                                                        saacxc AS c
                                                        ON c.codclie = a.codclie
                                                    JOIN
                                                        sasucursal AS d
                                                        ON d.id = c.fk_sucursal
                                                    WHERE
                                                        c.Saldo > 10
                                                        AND c.tipocxc IN (20)
                                                        AND c.tasadolar > 0
                                                        AND c.codclie = '$codclie'
                                                        $datafechas

                                                    ORDER BY  2

                                                          ";

        $saldocxc = \Illuminate\Support\Facades\DB::select($sqlcostoinv);

        $vista  = view('saacxclistado', compact( 'saldocxc', 'codclie', 'fecha1', 'fecha2'))->render();

        return response()->json(['success' => 'success', 'updated' => 1, 'vista' => $vista], 200);
    }

    public function cxcabonarweb(Request $request)
    {
        $codclie     = $request->codclie;
        $fecha1      = $request->fecha1;
        $fecha2      = $request->fecha2;
        $montoabonar = $request->montoabonar;

        $datafechas = '';

        if(isset($fecha1) and isset($fecha2) and $fecha1 != '' and $fecha2 != ''){
            $datafechas = " and  (c.fechat >= '$fecha1 00:00:00.00' and c.fechat <= '$fecha2 23:59:22') ";
        }

        $sqlcostoinv = "
                                                       SELECT d.id as sucu, d.descrip as sucursal, c.fechat,
                                                        'FACT' AS tipo,
                                                        c.numerod as numero,
                                                        a.descrip AS cliente,
                                                        date_format(c.fechat,'%d/%m/%Y') fecha,
                                                         (c.montodolares) AS credito,
                                                         (c.montodolares - (c.saldo / c.tasadolar)) AS abonado,
                                                        a.codclie,
                                                         (c.saldo / c.tasadolar) AS saldo,
                                                          (IFNULL(
                                                         ((  select totalmontodivisa
                                                            from safact g
                                                            where g.codclie='$codclie'
                                                            and g.numerod= c.numerod
                                                            and c.tipocxc = '10'
                                                            and g.fk_sucursal= c.fk_sucursal
                                                            and g.codclie = a.codclie
                                                            and g.tipofac = 'A'
                                                        )* ((c.saldo/c.tasadolar)/c.montodolares))
                                                    ,0)) as saldodivisa
                                                    FROM
                                                        saclie AS a
                                                    JOIN
                                                        saacxc AS c
                                                        ON c.codclie = a.codclie
                                                    JOIN
                                                        sasucursal AS d
                                                        ON d.id = c.fk_sucursal
                                                    WHERE
                                                        c.Saldo > 10
                                                        AND c.tipocxc IN (10)
                                                        AND c.tasadolar > 0
                                                        AND c.codclie = '$codclie'

                                                        $datafechas

                                                        UNION

                                                        SELECT d.id as sucu, d.descrip as sucursal, c.fechat,
                                                        'N.DEB' AS tipo,
                                                        c.numerod as numero,
                                                        a.descrip AS cliente,
                                                        date_format(c.fechat,'%d/%m/%Y') fecha,
                                                         (c.montodolares) AS credito,
                                                         (c.montodolares - (c.saldo / c.tasadolar)) AS abonado,
                                                        a.codclie,
                                                         (c.saldo / c.tasadolar) AS saldo,
                                                         0 as saldodivisa
                                                    FROM
                                                        saclie AS a
                                                    JOIN
                                                        saacxc AS c
                                                        ON c.codclie = a.codclie
                                                    JOIN
                                                        sasucursal AS d
                                                        ON d.id = c.fk_sucursal
                                                    WHERE
                                                        c.Saldo > 10
                                                        AND c.tipocxc IN (20)
                                                        AND c.tasadolar > 0
                                                        AND c.codclie = '$codclie'
                                                        $datafechas
                                                    ORDER BY  1
                                                          ";

        $saldocxc = \Illuminate\Support\Facades\DB::select($sqlcostoinv);
        $arraysucursal = array();

        foreach($saldocxc as $index => $cxc){
            if(!isset($arraysucursal[$cxc->sucu])){
                $arraysucursal[$cxc->sucu]['descrip'] = $cxc->sucursal;
                $arraysucursal[$cxc->sucu]['montoabonar'] = 0;
            }
            $arraysucursal[$cxc->sucu]['montoabonar'] += $cxc->saldo;
        }

        $resta = $montoabonar;

        foreach ($arraysucursal as $index => $item) {
            $aux = $item['montoabonar'];

            if($item['montoabonar'] > $resta){
                $aux = $resta;
            }

            if($resta > 0){ $resta = $resta - $aux;
                $newcxc = new Saacxc();
                $newcxc->tipocxc      = 99;
                $newcxc->nrounico     = 0;
                $newcxc->NroRegi      = 0;
                $newcxc->codesta      = 'web';
                $newcxc->CodUsua      = 'web';
                $newcxc->NumeroD      = 'web';
                $newcxc->NumeroN      = '';
                $newcxc->codoper      = 'web';
                $newcxc->codclie      = $codclie;
                $newcxc->codvend      = '01';
                $newcxc->document     = 'Abono web';
                $newcxc->Notas1       = '';
                $newcxc->Notas2       = '';
                $newcxc->Notas3       = '';
                $newcxc->descargar    = 1;
                $newcxc->EsUnPago     = 1;
                $newcxc->xdev         = 0;
                $newcxc->fk_transaccion = 0;
                $newcxc->Monto        = 0;
                $newcxc->MontoNeto    = 0;
                $newcxc->MtoTax       = 0;
                $newcxc->Saldo        = 0;
                $newcxc->SaldoOrg     = 0;
                $newcxc->BaseImpo     = 0;
                $newcxc->TExento      = 0;
                $newcxc->CancelA      = 0;
                $newcxc->CancelE      = 0;
                $newcxc->CancelT      = 0;
                $newcxc->CancelC      = 0;
                $newcxc->dolares      = 0;
                $newcxc->pesos        = 0;
                $newcxc->dolar_tranf  = 0;
                $newcxc->euros        = 0;
                $newcxc->tasadolar    = 0;
                $newcxc->tasapeso     = 0;
                $newcxc->tasaeuro     = 0;
                $newcxc->peso_tranf   = 0;
                $newcxc->cancelaUSD   = 0;
                $newcxc->FechaI       = Carbon::now();
                $newcxc->FechaE       = Carbon::now();
                $newcxc->FechaT       = Carbon::now();
                $newcxc->FechaV       = Carbon::now();
                $newcxc->montodolares = $aux;
                $newcxc->fk_sucursal  = $index;
                $newcxc->save();
            }
        }


        return response()->json(['success' => 'success', 'updated' => 1], 200);

    }

    public function cuentaxcobrar(Request $request)
    {
        $sucursalid = str_replace("300","",$request->sucursal);
        $cuentasporcobrar = $request->cuentasporcobrar;
        $cuentasporcobrar = json_decode($cuentasporcobrar);

        if(isset($cuentasporcobrar)){
            foreach ($cuentasporcobrar as $cxc){

                if(isset($cxc->NroUnico)){
                    $record = Saacxc::where(['NroUnico'=>  $cxc->NroUnico, 'fk_sucursal'=> $cxc->fk_sucursal])->first();

                    if($cxc->NroUnico > 0){
                        $oldtarjetas = Saipacxc::where(['NroPpal'=> $cxc->NroUnico, 'fk_sucursal'=> $cxc->fk_sucursal])->get();
                        if(isset($oldtarjetas) and count($oldtarjetas)>0){
                            foreach ($oldtarjetas as $oldtarjeta){
                                $oldtarjeta->delete();
                            }
                        }
                    }

                    if(!isset($record->id)) {
                        $record = new Saacxc();
                    }

                    if( isset($cxc->tarjetas)){
                        foreach ($cxc->tarjetas as $tarjeta){
                            $newtar  = new Saipacxc();
                            $auxitem = (array) $tarjeta;
                            $newtar->fill($auxitem);
                            $newtar->save();
                        }
                    }

                    $aux = (array) $cxc;
                    $record->fill($aux) ;
                    $record->fk_sucursal = $cxc->fk_sucursal;

                    $record->save();
                }
            }
        }

        return response()->json(['success' => 'success', 'updated' => 1], 200);
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show(Saacxc $saacxc)
    {
        //
    }

    public function edit(Saacxc $saacxc)
    {
        //
    }

    public function update(Request $request, Saacxc $saacxc)
    {
        //
    }

    public function destroy(Saacxc $saacxc)
    {
        //
    }


    public function descargado(Request $request)
    {
        $sucursalid = str_replace("300","",$request->sucursal);
        $idcxc = $request->idcxc;

        $saacxc = Saacxc::find($idcxc);
        if(isset($saacxc)){
            $saacxc->descargar = 2;
            $saacxc->save();
        }

        return response()->json(['success' => 'success', 'updated' => 1], 200);
    }

    public function descargar(Request $request)
    {
        $sucursalid = str_replace("300","",$request->sucursal);
        $auxsaacxc['saacxc'] = [];
        $saacxc =  Saacxc::selectRaw('id, montodolares, fk_sucursal, codclie')
                            ->whereRaw("     descargar = 1
                                         and tipocxc   = 99
                                         and montodolares > 0
                                         and fk_sucursal = $sucursalid")->first();
        if($saacxc){
            array_push($auxsaacxc['saacxc'] , $saacxc);
        }

        return response()->json(['success'=>'success', 'auxsaacxc' => $auxsaacxc]);
    }
}
