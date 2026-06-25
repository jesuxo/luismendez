<?php

namespace App\Http\Controllers;

use App\Models\Safact;
use App\Models\Saipavta;
use App\Models\Saitemfac;
use App\Models\Saseprfac;
use Illuminate\Http\Request;

class SafactController extends Controller
{
    public function documentoSafact(Request $request)
    {
        $ajax    = 0;
        $tipofac = $request->tipofac;
        $numerod = $request->numerod;
        $fk_sucu = $request->fksucu;

        $documentoId = Safact::where('NumeroD', $numerod)
            ->where('TipoFac', $tipofac)
            ->where('fk_sucursal', $fk_sucu)
            ->value('id');
//safact.numeror,
        $documento = Safact::selectRaw("
                date_format(fechat, '%d/%m/%Y') as fecha,
                date_format(fechat, '%h:%i %p') as hora,
                notas1, notas2, notas3, safact.NumeroD, TipoFac, fk_sucursal,
                cancelausd, codesta, descrip, id3, dolares, pesos,
                (cancele - efectivosumado) as cancele,
                (cancelt - tarjetasumado) as cancelt,
                vuelto_cancele, vuelto_dolares, vuelto_pesos,
                dolar_transf as transf,
                (credito/tasa_dolar) as credito,
                (contado/tasa_dolar) as contado,
                tasa_dolar
            ")
            ->with(['items.producto.instancia','sucursal'])
            ->whereHas('items', function ($q) use ($fk_sucu, $tipofac, $numerod) {
                $q->whereRaw("saitemfac.TipoFac = '$tipofac' and saitemfac.NumeroD='$numerod' and  saitemfac.fk_sucursal = $fk_sucu
               and safact.TipoFac = '$tipofac' and safact.NumeroD='$numerod' and  safact.fk_sucursal = $fk_sucu
                ");
            })
            ->where('id', $documentoId)
            ->first();

        $instpago = [];
        if ($documento && ($documento->cancelt > 0 || $documento->transf > 0)) {
            $instpago = Saipavta::with('satarj')
                ->where([
                    'NumeroD' => $numerod,
                    'TipoFac' => $tipofac,
                    'fk_sucursal' => $fk_sucu
                ])
                ->get();
        }

        return view('documentoventa', compact('ajax', 'instpago', 'numerod', 'tipofac', 'documento'));
    }

    public function documentoAjax(Request $request)
    {
        $ajax    = 1;
        $tipofac = $request->tipofac;
        $numerod = $request->numerod;
        $fk_sucu = $request->fksucu;

        $documentoId = Safact::where('NumeroD', $numerod)
            ->where('TipoFac', $tipofac)
            ->where('fk_sucursal', $fk_sucu)
            ->value('id');
//safact.numeror,
        $documento = Safact::selectRaw("
                date_format(fechat, '%d/%m/%Y') as fecha,
                date_format(fechat, '%h:%i %p') as hora,
                notas1, notas2, notas3, safact.NumeroD, TipoFac, fk_sucursal,
                cancelausd, codesta, descrip, id3, dolares, pesos,
                (cancele - efectivosumado) as cancele,
                (cancelt - tarjetasumado) as cancelt,
                vuelto_cancele, vuelto_dolares, vuelto_pesos,
                dolar_transf as transf,
                (credito/tasa_dolar) as credito,
                (contado/tasa_dolar) as contado,
                tasa_dolar
            ")
            ->with(['items.producto.instancia','sucursal'])
            ->whereHas('items', function ($q) use ($fk_sucu, $tipofac, $numerod) {
                $q->whereRaw("saitemfac.TipoFac = '$tipofac' and saitemfac.NumeroD='$numerod' and  saitemfac.fk_sucursal = $fk_sucu
               and safact.TipoFac = '$tipofac' and safact.NumeroD='$numerod' and  safact.fk_sucursal = $fk_sucu
                ");
            })
            ->where('id', $documentoId)
            ->first();

        $instpago = [];
        if ($documento && ($documento->cancelt > 0 || $documento->transf > 0)) {
            $instpago = Saipavta::with('satarj')
                ->where([
                    'NumeroD' => $numerod,
                    'TipoFac' => $tipofac,
                    'fk_sucursal' => $fk_sucu
                ])
                ->get();
        }

        return view('layouts.documento', compact('ajax','instpago', 'numerod', 'tipofac', 'documento'))->render();
    }

    public function documento(Request $request)
    {
        $sucursalid = str_replace("300","",$request->sucursal);
        $facturas = $request->facturas;
        $facturas = json_decode($facturas);
        $vector   = [];

        if(isset($facturas)){
            foreach ($facturas as $fac){

                if(isset($fac->nrounico)){
                    $record = Safact::where(['nrounico'=>  $fac->nrounico, 'fk_sucursal'=> $sucursalid])->first();

                    $additems = 0;
                    $aux = (array) $fac;

                    if(isset($record) and isset($record->id) and $record->id >0){
                        $record = Safact::find($record->id);
                        $record->fill($aux) ;
                        $record->save();
                        $vector[$fac->nrounico] = 1;
                    }else{

                        $record = new Safact();
                        $record->fill($aux) ;
                        $additems = 1;

                        $record->fk_sucursal = $sucursalid ;

                        if($additems and isset($fac->allitems)){
                            foreach ($fac->allitems as $allitem){
                                $newitem = new Saitemfac();
                                $auxitem = (array) $allitem;
                                $newitem->fill($auxitem);
                                $newitem->fk_sucursal = $sucursalid ;
                                $newitem->save();
                            }
                        }

                        if($additems and isset($fac->tarjetas)){
                            foreach ($fac->tarjetas as $tarjeta){
                                $newtar  = new Saipavta();
                                $auxitem = (array) $tarjeta;
                                $newtar->fill($auxitem);
                                $newtar->fk_sucursal = $sucursalid ;
                                $newtar->save();
                            }
                        }

                        if($additems and isset($fac->seriales)){
                            foreach ($fac->seriales as $seriales){
                                $newser = new Saseprfac();
                                $auxser = (array) $seriales;
                                $newser->fill($auxser);
                                $newser->fk_sucursal = $sucursalid ;
                                $newser->save();
                            }
                        }
                        $record->save();
                        $vector[$fac->nrounico] = 1;
                    }
                }
            }
            return response()->json(['success' => 'success', 'vector' => $vector], 200);
        }

        return response()->json(['json' => 'json', ], 200);
    }

    public function index()
    {
        //
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show(Safact $safact)
    {
        //
    }

    public function edit(Safact $safact)
    {
        //
    }

    public function update(Request $request, Safact $safact)
    {
        //
    }

    public function destroy(Safact $safact)
    {
        //
    }
}
