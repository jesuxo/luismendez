
<style>
    .card-header {
        border-bottom: 1px solid #0c192c;
    }
</style>
<div class="row">
    <div class="col-lg-12">
        <div class="card-header border-bottom-dashed p-4 pt-0">
            <div class="d-sm-flex">
                <div class="flex-grow-1">
                    <img src="{{ URL::asset('build/images/logo-dark.png') }}" class="card-logo card-logo-dark"
                         alt="logo dark" width="275px">
                    <img src="{{ URL::asset('build/images/logo-light.png') }}" class="card-logo card-logo-light"
                         alt="logo light"   width="275px">

                </div>
                <div class="flex-shrink-0 mt-sm-0 mt-3" style="text-align: right">
                    <h6><span class="text-muted fw-normal">Sucursal: &nbsp;</span> <span id="legal-register-no">{{(isset($documento->sucursal) and isset($documento->sucursal->descrip))? $documento->sucursal->descrip : ''}} </span></h6>
                    <h6><span class="text-muted fw-normal">Estaci&oacute;n: &nbsp;</span> <span  > {{$documento->codesta}}</span></h6>
                    <h6><span class="text-muted fw-normal">Cliente: &nbsp;</span> <span  > {{$documento->descrip}}</span></h6>
                    <h6><span class="text-muted fw-normal">Cedula/Rif: &nbsp;</span> <span  > {{$documento->id3}}</span></h6>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-12">
        <div class="card-body p-4">
            <div class="row g-3">
                <table>
                    <tr>
                        <td>
                            <p class="text-muted mb-2 text-uppercase fw-semibold fs-14">DOCUMENTO NRO</p>
                            <h5 class="fs-15 mb-0">
                                <a id="invoice-no" h.0
                                   0
                                   0
                                   ref="/doc/{{$documento->TipoFac}}/{{$documento->NumeroD}}/{{$documento->fk_sucursal}}" >
                                        {{($documento->TipoFac=='A')?'Factura': 'Devolucion'}} {{$numerod}}
                                </a>
                            </h5>
                        </td>
                        @if(isset($documento->numeror) and $documento->numeror != '')
                        <td>
                            <p class="text-muted mb-2 text-uppercase fw-semibold fs-14">Factura Devuelta</p>
                            <h5 class="fs-15 mb-0">
                                <span id="invoice-date">{{$documento->numeror}}</span>
                            </h5>
                        </td>
                        @endif
                        <td>
                            <p class="text-muted mb-2 text-uppercase fw-semibold fs-14">FECHA</p>
                            <h5 class="fs-15 mb-0">
                                <span id="invoice-date">{{$documento->fecha}}</span>
                                <small class="text-muted" id="invoice-time">{{$documento->hora}}</small>
                            </h5>
                        </td>

                        <td>
                            <p class="text-muted mb-2 text-uppercase fw-semibold fs-14">  Contado</p>
                            <h5 class="fs-15 mb-0">$<span id="total-amount">{{number_format($documento->contado,2,',','.')}}</span></h5>
                        </td>
                        <td>
                            <p class="text-muted mb-2 text-uppercase fw-semibold fs-14"> Cr&eacute;dito</p>
                            <h5 class="fs-15 mb-0">$<span id="total-amount">{{number_format($documento->credito,2,',','.')}}</span></h5>
                        </td>
                    </tr>
                </table>

            </div>
        </div>
    </div>

    <div class="col-lg-12">
        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table table-borderless text-center table-nowrap align-middle mb-0">
                    <thead>
                    <tr class="table-active">
                        <th width="1%" >#</th>
                        <th width="54%" class="text-start">Detalle Producto/Servicio </th>
                        <th width="15%" class="text-end">Precio</th>
                        <th width="15%" class="text-center">Cantidad</th>
                        <th width="15%" class="text-end">Total</th>
                    </tr>
                    </thead>
                    <tbody id="products-list">
                    @foreach($documento->items as $index => $item)
                        @if($item->fk_sucursal == $documento->fk_sucursal)
                            <tr @if(($index%2)!=0) bgcolor="#f5f8fb" @endif>
                                <th scope="row">{{$index+1}}</th>
                                <td class="text-start">
                                    <span class="fw-medium">{{ $item->Descrip1 }}</span>
                                    <p class="text-muted mb-0">
                                        {{(isset($item->producto))? $item->producto->instancia->descrip : ''}}
                                        {{(isset($item->producto) and $item->producto->refere !='')? $item->producto->refere : ''}}
                                        {{(isset($item->producto) and $item->producto->marca  !='')? $item->producto->marca  : ''}}
                                    </p>
                                </td>
                                <td class="text-end">${{number_format($item->costod,2,',','.')}}</td>
                                <td class="text-center">{{$item->Cantidad+0}}</td>
                                <td class="text-end">${{number_format($item->costod*$item->Cantidad,2,',','.')}}</td>
                            </tr>
                        @endif
                    @endforeach
                    </tbody>
                </table>
                <!--end table-->
            </div>

            <div class="mt-3">
                <table width="100%" border="0">
                    <tr>
                        <td width="50%" valign="top">
                            <h6 class=" text-uppercase fw-semibold mb-3"  style="color: #0c192c !important;">INFORMACION ADICIONAL</h6>
                        </td>
                        <td width="38%" valign="top" >
                            <h6 class=" text-uppercase fw-semibold mb-3"  style="color: #0c192c !important;">METODOS DE PAGO</h6>
                        </td>
                        <td width="12%" valign="top" align="right">
                            BCV {{number_format($documento->tasa_dolar,2,',','.')}}
                        </td>
                    </tr>
                    <tr>
                        <td width="50%" valign="top">
                            <p class="  mb-1" style="color: #0c192c !important;"><b>NOTAS1:</b> {{$documento->notas1}} </p>
                            <p class="  mb-1" style="color: #0c192c !important;"><b>NOTAS2:</b> {{$documento->notas2}} </p>
                            <p class="  mb-1" style="color: #0c192c !important;"><b>NOTAS3:</b> {{$documento->notas3}} </p>
                        </td>
                        <td width="38%" valign="top">
                            @if($documento->cancele != 0)
                                <p class="  mb-1" style="color: #0c192c important;"><b>EfectivoBs:</b>  </p>
                            @endif
                            @if($documento->vuelto_cancele != 0)
                                <p class="  mb-1" style="color: #0c192c important;"><b>Vueltos Bs:</b>   </p>
                            @endif
                            @if($documento->dolares != 0)
                                <p class="  mb-1" style="color: #0c192c important;"><b>EfectivoUSD:</b>  </p>
                            @endif
                            @if($documento->vuelto_dolares != 0)
                                <p class="  mb-1" style="color: #0c192c important;"><b>Vueltos USD:</b>    </p>
                            @endif
                            @if($documento->pesos != 0)
                                <p class="  mb-1" style="color: #0c192c important;"><b>Efectivo PESOS:</b>    </p>
                            @endif
                            @if($documento->vuelto_pesos != 0)
                                <p class="  mb-1" style="color: #0c192c important;"><b>Vueltos PESOS:</b>    </p>
                            @endif

                            @if($documento->cancelausd != 0)
                                <p class="  mb-1" style="color: #132659 important;"><b>ANTICIPO APLICADO USD:</b>    </p>
                            @endif

                            @if(isset($instpago) and count($instpago)>0)
                                @foreach($instpago as $index => $data)
                                    {{(isset($data->satarj) and isset($data->satarj->descrip))? $data->satarj->descrip: ''}}<br>
                                @endforeach
                            @endif

                        </td>
                        <td width="12%" valign="top" align="right">
                            @if($documento->cancele != 0)
                                <p class="  mb-1" style="color: #0c192c important;">  {{number_format($documento->cancele,2,',','.')}} </p>
                            @endif
                            @if($documento->vuelto_cancele != 0)
                                <p class="  mb-1" style="color: #0c192c important;"> {{number_format($documento->vuelto_cancele,2,',','.')}} </p>
                            @endif
                            @if($documento->dolares != 0)
                                <p class="  mb-1" style="color: #0c192c important;">  {{number_format($documento->dolares,2,',','.')}} </p>
                            @endif
                            @if($documento->vuelto_dolares != 0)
                                <p class="  mb-1" style="color: #0c192c important;">  {{number_format($documento->vuelto_dolares,2,',','.')}} </p>
                            @endif
                            @if($documento->pesos != 0)
                                <p class="  mb-1" style="color: #0c192c important;">  {{number_format($documento->pesos,2,',','.')}} </p>
                            @endif
                            @if($documento->vuelto_pesos != 0)
                                <p class="  mb-1" style="color: #0c192c important;">  {{number_format($documento->vuelto_pesos,2,',','.')}} </p>
                            @endif

                            @if($documento->cancelausd != 0)
                                <p class="  mb-1" style="color: #132659 important;"> {{number_format($documento->cancelausd,2,',','.')}} </p>
                            @endif
                            @if(isset($instpago) and count($instpago)>0)
                                @foreach($instpago as $index => $data)
                                    @if(isset($data->dolares) and $data->dolares > 0)
                                        {{number_format($data->dolares,2,',','.')}}
                                    @else
                                            @if(isset($data->pesos) and $data->pesos > 0)
                                                {{number_format($data->pesos,2,',','.')}}
                                            @else
                                                @if(isset($data->Monto) and $data->Monto > 0)
                                                    {{number_format($data->Monto,2,',','.')}}
                                                @else
                                                @endif
                                            @endif
                                    @endif

                                            <br>
                                @endforeach
                            @endif
                        </td>
                    </tr>
                </table>
            </div>

            @if(!$ajax)
            <div class="hstack gap-2 justify-content-end d-print-none mt-4">
                <a href="javascript:window.print()" class="btn btn-success"><i
                        class="ri-printer-line align-bottom me-1"></i> Print</a>
            </div>
            @endif
        </div>
    </div>
</div>
