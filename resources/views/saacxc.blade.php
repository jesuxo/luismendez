@extends('layouts.master')
@section('title')
    Reporte CxC
@endsection
@section('css')

    <style>
        #clearall{
            text-decoration: none !important;
        }

         .botoncal{
             background: transparent;
             border: none;
             color: white;
         }
        .botoncal:hover{
            font-size: 13px;
        }
    </style>

@endsection
@section('content')
    <style>
        input::-webkit-outer-spin-button,
        input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        /* For Firefox */
        input[type="number"] {
            -moz-appearance: textfield;
        }
        .tdline{
            border:1px solid #0072c5 !important;

        }
        .tdlineff{
            border-left:1px solid #fff !important;

            color: white !important;
            background-color: #0072c5 !important;
        }
    </style>
    <x-breadcrumb title="Reporte CxC" pagetitle="CxC" />

    <div class="row">
        <div class="col-xl-4 ">
            <form  method="post" name="form1" id="form1" action="/cxc{{(isset($id) and $id > 0)? '/'.$id : ''}}">
                <div class="col-md-12 mb-2 order-last">

                    <div class="input-group">
                        <input type="text" class="form-control" data-provider="flatpickr" placeholder="CXC entre fechas"
                               data-range-date="true" data-date-format="d/m/Y"
                               data-deafult-date="" name="fechasreport" readonly="readonly" value="{{$fechasreport}}"
                        >
                        <div class="input-group-text bg-primary border-primary text-white">
                            <button type="submit" class="botoncal" >Consultar</button>
                        </div>
                    </div>

                </div>
                @csrf
                @method('POST')
            </form>
            <div class="card overflow-hidden">
                <div class="accordion accordion-flush filter-accordion">
                    <div class="card-body border-bottom">
                        <div class="table-responsive table-card ">
                            <table width="100%" border="0"    class="table table-borderless table-centered align-middle table-nowrap mb-0 ">
                                <tr bgcolor="#fff">
                                    <td width="30%" height="30"align="left" class="tdline" >SUCURSAL</td>
                                    <td width="10%" align="center" class="tdlineff" > CANT</td>
                                    <td width="30%" align="center" class="tdlineff" > SALDO Bs</td>
                                    <td width="30%" align="center" class="tdlineff" > SALDO USD</td>
                                </tr>
                                @php
                                    $nn = $tcanti = $tmonto = $tabona = $tdivis = 0;

                                    $datafechas ='';
                                    if(isset($fecha1) and isset($fecha2) and $fecha1 != '' and $fecha2 != ''){
                                        $datafechas = " and  (c.fechat >= '$fecha1 00:00:00.00' and c.fechat <= '$fecha2 23:59:22') ";
                                    }
                                @endphp

                                @foreach($sucursales as $index => $sucu)

                                    @php

                                           $sql = "
                                                   SELECT
                                                   COUNT(*) AS cant,
                                                   SUM(c.montodolares) AS credito,
                                                   SUM(c.montodolares - (c.saldo / c.tasadolar)) AS abonado,
                                                   SUM(c.saldo / c.tasadolar) AS saldo,
                                                   sum(IFNULL(
                                                         ((  select totalmontodivisa
                                                            from safact g
                                                            where g.fk_sucursal = ".$sucu->id."
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
                                               WHERE
                                                   c.Saldo > 10
                                                   $datafechas
                                                   AND c.tipocxc IN (20, 10)
                                                   AND c.tasadolar > 0
                                                   AND c.fk_sucursal =  ".$sucu->id;

                                       $saldocxc = \Illuminate\Support\Facades\DB::select($sql);

                                       if($saldocxc[0]->saldo != 0){
                                            $nn++;
                                            $tcanti += $saldocxc[0]->cant;
                                            $tmonto += $saldocxc[0]->saldo;
                                            $tdivis += $saldocxc[0]->saldodivisa;
                                    @endphp

                                        <tr @php if(($nn%2)==0){echo 'bgcolor="#eee"'; }else{echo 'bgcolor="#fff"';} @endphp>
                                            <td  height="30"align="left" class="tdline" >
                                                <a href="{{route('saacxc',['id'=>$sucu->id, 'fechasreport'=>$fechasreport])}}"  class="mb-0 listname" style=" font-size: 12px;  ">
                                                    {{$sucu->descrip}}
                                                </a>
                                            </td>
                                            <td align="center" class="tdline">  {{($saldocxc[0]->cant  != 0 )?  number_format( $saldocxc[0]->cant ,0,',','.').'  ' : ''}}</td>
                                            <td align="right"  class="tdline">  {{($saldocxc[0]->saldo != 0 )?  number_format($saldocxc[0]->saldo,2,',','.'):''}}</td>
                                            <td align="right"  class="tdline">  {{($saldocxc[0]->saldodivisa != 0 )?  number_format($saldocxc[0]->saldodivisa,2,',','.'):''}}</td>
                                        </tr>
                                    @php  } @endphp

                                @endforeach
                                <tr >
                                    <td height="30"align="left"></td>
                                    <td align="center"></td>
                                    <td align="center"></td>
                                    <td align="center"></td>
                                </tr>
                                <tr >
                                    <td height="30"align="left" class="tdline">TOTALES </td>
                                    <td align="center" class="tdline" >{{($tcanti != 0)? number_format( $tcanti ,0,',','.') : ''}} </td>
                                    <td align="center" class="tdline" >{{($tmonto != 0)? number_format($tmonto ,2,',','.'):''}} </td>
                                    <td align="center" class="tdline" >{{($tdivis != 0)? number_format($tdivis ,2,',','.'):''}} </td>
                                </tr>


                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-8">
            @if(isset($sucursalselected)    and isset($sucursalselected->descrip))

                <div class="card overflow-hidden">
                    <div class="accordion accordion-flush filter-accordion">
                        <div class="card-body border-bottom">
                            <div class="table-responsive table-card ">
                                <table width="100%" border="0" class="table table-borderless table-centered align-middle table-nowrap mb-0 ">
                                    <tr bgcolor="#fff">
                                        <td width="45%" height="30"align="left" class="tdlineff" >CLIENTES - {{$sucursalselected->descrip}}</td>
                                        <td width="11%" align="center" class="tdlineff" > CANT</td>
                                        <td width="11%" align="center" class="tdlineff" > FACTURADO</td>
                                        <td width="11%" align="center" class="tdlineff" > ABONADO</td>
                                        <td width="11%" align="center" class="tdlineff" > SALDO Bs</td>
                                        <td width="11%" align="center" class="tdlineff" > SALDO USD</td>
                                    </tr>

                                    @php
                                        $nn     = 1;
                                        $tmonto = $tabona = $tsaldo = $tdivis = 0;

                                            $fk_sucursal = $sucursalselected->id;

                                                  $sqlcostoinv = "
                                                       SELECT
                                                        COUNT(*) AS deudas,
                                                        a.descrip AS cliente,
                                                        SUM(c.montodolares) AS credito,
                                                        SUM(c.montodolares - (c.saldo / c.tasadolar)) AS abonado,
                                                        a.codclie,
                                                        SUM(c.saldo / c.tasadolar) AS saldo,
                                                        sum(IFNULL(
                                                         ((  select totalmontodivisa
                                                            from safact g
                                                            where g.fk_sucursal = ".$sucursalselected->id."
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
                                                    WHERE
                                                        c.Saldo > 10
                                                        $datafechas
                                                        AND c.tipocxc IN (20, 10)
                                                        AND c.tasadolar > 0
                                                        AND c.fk_sucursal = ".$sucursalselected->id."
                                                    GROUP BY
                                                        a.descrip, a.codclie;

                                                          ";

                                        $saldocxc = \Illuminate\Support\Facades\DB::select($sqlcostoinv);

                                        @endphp
                                            @foreach($saldocxc as $index => $cxc)
                                                @php
                                                 $nn++;
                                                 $tmonto += $cxc->credito;
                                                 $tabona += $cxc->abonado;
                                                 $tsaldo += $cxc->saldo;
                                                 $tdivis += $cxc->saldodivisa;
                                                @endphp
                                                <tr @php if(($nn%2)==0){echo 'bgcolor="#eee"'; }else{echo 'bgcolor="#fff"';} @endphp>
                                                    <td  height="30"align="left" class="tdline" >  <a href="/clientes/{{$cxc->codclie}}">{{$cxc->cliente}} </a> </td>
                                                    <td align="right" class="tdline" >
                                                        <button type="button" class="btn btn-outline-primary cxcmodal"
                                                                data-codclie="{{$cxc->codclie}}"
                                                                data-fecha1="{{(isset($fecha1))? $fecha1 :''}}"
                                                                data-fecha2="{{(isset($fecha2))? $fecha2 :''}}"
                                                                onclick="$('#titulolistado').html('FACTURAS A CREDITO DE {{$cxc->cliente}}')"
                                                                data-bs-toggle="modal" data-bs-target="#cxcmodal">
                                                            <i class="bx bx-menu"></i> <span class="badge bg-success ms-1">{{$cxc->deudas}}</span>
                                                        </button>

                                                    </td>
                                                    <td align="right" class="tdline" > {{($cxc->credito != 0 )? number_format( $cxc->credito ,2,',','.').'  ' : ''}}</td>
                                                    <td align="right" class="tdline" > {{($cxc->abonado != 0 )? number_format( $cxc->abonado ,2,',','.').'  ' : ''}}</td>
                                                    <td align="right" class="tdline" > {{($cxc->saldo   != 0 )? number_format($cxc->saldo,2,',','.'):''}}</td>
                                                    <td align="right" class="tdline" > {{($cxc->saldodivisa   != 0 )? number_format($cxc->saldodivisa,2,',','.'):''}}</td>
                                                </tr>

                                            @endforeach

                                    <tr >
                                        <td height="30"align="left"  > </td>
                                        <td  align="center"></td>
                                        <td  align="center"></td>
                                        <td  align="center"></td>
                                        <td  align="center"></td>
                                        <td  align="center"></td>
                                    </tr>
                                    <tr >
                                        <td height="30"align="left" class="tdline " >TOTALES </td>
                                        <td align="right" class="tdline" >  </td>
                                        <td align="right" class="tdline" >  {{($tmonto != 0)? number_format($tmonto ,2,',','.')  : ''}} </td>
                                        <td align="right" class="tdline" >  {{($tabona != 0)? number_format($tabona ,2,',','.')  : ''}} </td>
                                        <td align="right" class="tdline" >  {{($tsaldo != 0)? number_format($tsaldo ,2,',','.')  : ''}} </td>
                                        <td align="right" class="tdline" >  {{($tdivis != 0)? number_format($tdivis ,2,',','.')  : ''}} </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal fade" id="cxcmodal" aria-hidden="true" aria-labelledby="..." tabindex="-1">
                    <div class="modal-dialog modal-xl modal-dialog-scrollable">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="titulolistado"> LISTADO FACTURAS A CREDITO</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                                </button>
                            </div>
                            <div  class="modal-body"  id="contentcxcreport"></div>

                        </div>
                    </div>
                </div>
            @else
                <div class="row gy-4">
                    @if(isset($cxcprocesos) and count($cxcprocesos) > 0)

                        @foreach($cxcprocesos as $proceso)
                            <div class="col-lg-6">
                                <div class="card bg-opacity-10 bg-{{($proceso->descargar == 1)? 'info' : 'success'}} border-{{($proceso->descargar == 1)? 'info' : 'success'}} border-opacity-50 mb-0">
                                    <div class="card-body">
                                        <h5 class="fs-18 mb-3">{{$proceso->cliente->descrip}}    </h5>
                                        <div class=" ">
                                            <div class="d-flex justify-content-between">
                                                <h6 class="fw-medium mb-1">{{$proceso->sucursalcli->descrip}} - ${{number_format($proceso->montodolares,2,',','.')}} </h6>
                                                <div class="badge badge-soft-{{($proceso->descargar == 1)? 'info' : 'success'}}   clearfix">
                                                    {{($proceso->descargar == 1)? 'Pendiente' : 'Procesado'}} {{$proceso->formattedDate}}
                                                </div>
                                            </div>
                                            <div class="flex-shrink-0" style="display: none">
                                                <a href="#!" class="text-reset stretched-link">View <i class="ph-arrow-right align-middle"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                    @endif

                </div>
            @endif
        </div>
    </div>
@endsection
@section('scripts')
    <!-- App js -->
    <script src="{{ URL::asset('build/js/app.js') }}"></script>
    <script>


        $('.cxcmodal').unbind('click').bind('click',function () {

            var codclie = $(this).attr('data-codclie');
            var fecha1  = $(this).attr('data-fecha1');
            var fecha2  = $(this).attr('data-fecha2');

            $('#contentcxcreport').html('<button class="btn btn-outline-primary btn-load"><span class="d-flex align-items-center"><span class="spinner-border flex-shrink-0" role="status"> <span class="visually-hidden"> Cargando...</span> </span> <span class="flex-grow-1 ms-2">Cargando... </span> </span> </button>');

            $.ajax({
                type:'post',
                data:{codclie: (codclie)? codclie : '',fecha1: (fecha1)? fecha1 : '',fecha2: (fecha2)? fecha2 : '' },
                url:'/cxclist',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success:function(response) {

                    if(response.vista) {
                        $('#contentcxcreport').html(response.vista);
                    }else{
                        $('#contentcxcreport').html('error');
                    }
                }
            });

        });


        function cxcabonarweb(codclie, fecha1, fecha2) {

            var montoabonar = parseFloat(document.getElementById("montoabonar").value);
            document.getElementById("alertmontoabonar").textContent = '';

            if(montoabonar && !isNaN(montoabonar) && montoabonar > 0) {

                $('#contentcxcreport').html('<button class="btn btn-outline-primary btn-load"><span class="d-flex align-items-center"><span class="spinner-border flex-shrink-0" role="status"> <span class="visually-hidden"> Cargando...</span> </span> <span class="flex-grow-1 ms-2">Cargando... </span> </span> </button>');

                $.ajax({
                    type: 'post',
                    data: {
                        codclie: codclie ? codclie : '',
                        fecha1 : fecha1  ? fecha1  : '',
                        fecha2 : fecha2  ? fecha2  : '',
                        montoabonar: montoabonar
                    },
                    url: '/cxcabonarweb',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {

                        window.location.href = "/cxc";
                    },
                    error: function(xhr, status, error) {
                        console.error('Error en el abono:', error);
                        $('#contentcxcreport').html('<div class="alert alert-danger">Error al procesar el abono</div>');
                    }
                });

            } else {
                document.getElementById("alertmontoabonar").textContent = 'Debe ingresar un monto válido mayor a 0';
            }
        }



    </script>
@endsection
