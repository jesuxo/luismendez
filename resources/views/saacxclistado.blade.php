<div>

    <div class="row">
        <div class="col-xl-12">
            <div class="card overflow-hidden">
                <div class="accordion accordion-flush filter-accordion">
                    <div class="card-body border-bottom">
                        <div class="table-responsive table-card ">
                            <table width="100%" border="0" class="table table-borderless table-centered align-middle table-nowrap mb-0 ">
                                <tr bgcolor="#fff">
                                    <td width="33%" height="30"align="left" class="tdlineff" >CLIENTE - {{$codclie}}</td>
                                    <td width="11%" align="center" class="tdlineff" > TIPO</td>
                                    <td width="11%" align="center" class="tdlineff" > FECHA</td>
                                    <td width="11%" align="center" class="tdlineff" > NUMERO</td>
                                    <td width="11%" align="center" class="tdlineff" > FACTURADO</td>
                                    <td width="11%" align="center" class="tdlineff" > ABONADO</td>
                                    <td width="11%" align="center" class="tdlineff" > SALDO Bs</td>
                                    <td width="11%" align="center" class="tdlineff" > SALDO USD</td>
                                </tr>

                                @php
                                    $nn     = 1;
                                    $tmonto = $tabona = $tsaldo = $tdivis = 0;



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
                                        <td  height="30"align="left" class="tdline" >  {{$cxc->cliente}} - {{$cxc->sucursal}}  </td>
                                        <td align="center" class="tdline" > {{$cxc->tipo}}</td>
                                        <td align="center" class="tdline" > {{$cxc->fecha}}</td>
                                        <td align="center" class="tdline" > {{$cxc->numero}}</td>
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
                                    <td  align="center"></td>
                                    <td  align="center"></td>
                                </tr>
                                <tr >
                                    <td height="30"align="left" class="tdline " >TOTALES </td>
                                    <td align="right" class="tdline" >  </td>
                                    <td align="right" class="tdline" >  </td>
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
        </div>
    </div>

</div>
<div class="modal-footer abonarfooter  " style="padding: 0 !important;  display: unset">
    <div class="row" style="justify-content: left !important;">

        <div class="col-8" >
            <table class="tdline" width="100%">
                <tr>
                    <td align="right" width="80%" class="tdline">
                        <input type="number"  id="montoabonar" style="width: 100%; max-width: 100%"
                               onchange="cxcabonarweb('{{$codclie}}','{{(isset($fecha1)? $fecha1: '')}}','{{(isset($fecha2)? $fecha2: '')}}')"  size="1" class="form-control"
                               placeholder="Ingrese el monto a abonoar Ej: 10.5 " />
                    </td>
                    <td align="center" width="20%" class="tdline">
                        <button type="button" class="btn btn-primary " onclick="cxcabonarweb('{{$codclie}}','{{(isset($fecha1)? $fecha1: '')}}','{{(isset($fecha2)? $fecha2: '')}}')"  >  ABONAR</button></td>
                </tr>
            </table>
        </div>
        <div class="col-2 text-end" >
            <button type="button" class="btn btn-light" data-bs-dismiss="modal">  CERRAR</button>
        </div>
        <div id="alertmontoabonar"  class="col-12"></div>
    </div>
</div>

